<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Exception\InvalidArgumentException;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;
use Socket\Raw\Exception;
use Socket\Raw\Factory;
use Socket\Raw\Socket;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var bool
     */
    private $secure;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var int
     */
    private $timeout;

    /**
     * Constructor.
     *
     * @param string $host     The hostname of the NNTP server.
     * @param int    $port     The port of the NNTP server.
     * @param bool   $secure   A bool indicating if a secure connection should be established.
     * @param int    $timeout  The socket timeout in seconds.
     * @param Factory $factory The socket client factory.
     */
    public function __construct($host, $port, $secure = false, $timeout = 15, Factory $factory = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->timeout = $timeout;
        $this->factory = $factory ?: new Factory();
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $address = gethostbyname($this->host);
        $url = $this->getSocketUrl($address);

        try {
            $this->socket = $this->factory->createFromString($url, $scheme)
                ->connectTimeout($url, $this->timeout);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Connection to %s:%d failed: %s', $address, $this->port, $e->getMessage()), 0, $e);
        }

        if ($this->secure) {
            stream_socket_enable_crypto($this->socket->getResource(), true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }

        $this->socket->setBlocking(false);

        return $this->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        $this->socket
            ->shutdown()
            ->close();
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        $commandString = $command->execute();

        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($commandString) > 510) {
            throw new InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (!$this->socket->selectWrite() || strlen($commandString."\r\n") !== $this->socket->write($commandString."\r\n")) {
            throw new RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse();

        if ($command->isMultiLine() && ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 399)) {
            $response = $command->isCompressed() ? $this->getCompressedResponse($response) : $this->getMultiLineResponse($response);
        }

        if (in_array($response->getStatusCode(), array(Response::COMMAND_UNKNOWN, Response::COMMAND_UNAVAILABLE))) {
            throw new RuntimeException('Sent command is either unknown or unavailable on server');
        }

        $expectedResponseCodes = $command->getExpectedResponseCodes();

        // Check if we received a response expected by the command.
        if (!isset($expectedResponseCodes[$response->getStatusCode()])) {
            throw new RuntimeException(sprintf(
                'Unexpected response received: [%d] %s',
                $response->getStatusCode(),
                $response->getMessage()
            ));
        }

        $expectedResponseHandler = $expectedResponseCodes[$response->getStatusCode()];
        if (!is_callable(array($command, $expectedResponseHandler))) {
            throw new RuntimeException(sprintf('Response handler (%s) is not callable method on given command object', $expectedResponseHandler));
        }

        $command->setResponse($response);
        $command->$expectedResponseHandler($response);

        return $command;
    }

    public function sendArticle(CommandInterface $command)
    {
        $commandString = $command->execute();

        if (!$this->socket->selectWrite() || strlen($commandString."\r\n.\r\n") !== $this->socket->write($commandString."\r\n.\r\n")) {
            throw new RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse();

        $expectedResponseCodes = $command->getExpectedResponseCodes();

        // Check if we received a response expected by the command.
        if (!isset($expectedResponseCodes[$response->getStatusCode()])) {
            throw new RuntimeException(sprintf(
                'Unexpected response received: [%d] %s',
                $response->getStatusCode(),
                $response->getMessage()
            ));
        }

        $expectedResponseHandler = $expectedResponseCodes[$response->getStatusCode()];
        if (!is_callable(array($command, $expectedResponseHandler))) {
            throw new RuntimeException(sprintf('Response handler (%s) is not callable method on given command object', $expectedResponseHandler));
        }

        $command->setResponse($response);
        $command->$expectedResponseHandler($response);

        return $command;
    }

    protected function getResponse()
    {
        $buffer = '';

        while ($this->socket->selectRead($this->timeout)) {
            $buffer .= $this->socket->read(1024);

            if ("\r\n" === substr($buffer, -2)) {
                break;
            }

            if ($buffer === false) {
                $this->disconnect();
                throw new RuntimeException('Incorrect data received from buffer');
            }
        }

        return Response::createFromString($buffer);
    }

    public function getMultiLineResponse(Response $response)
    {
        $buffer = '';

        while ($this->socket->selectRead($this->timeout)) {
            $buffer .= $this->socket->read(1024);

            if ("\n.\r\n" === substr($buffer, -4)) {
                break;
            }

            if ($buffer === false) {
                $this->disconnect();
                throw new RuntimeException('Incorrect data received from buffer');
            }
        }

        $lines = explode("\r\n", trim($buffer));
        if (end($lines) === '.') {
            array_pop($lines);
        }

        $lines = array_filter($lines);
        $lines = \SplFixedArray::fromArray($lines);

        return new MultiLineResponse($response, $lines);
    }

    public function getCompressedResponse(Response $response)
    {
        // Determine encoding by fetching first line.
        $line = $this->socket->read(1024);

        $uncompressed = '';

        while ($this->socket->selectRead($this->timeout)) {
            $buffer = $this->socket->read(1024);

            if (strlen($buffer) === 0) {
                $uncompressed = @gzuncompress($line);

                if ($uncompressed !== false) {
                    break;
                }
            }

            if ($buffer === false) {
                $this->disconnect();
                throw new RuntimeException('Incorrect data received from buffer');
            }

            $line .= $buffer;
        }

        $lines = explode("\r\n", trim($uncompressed));
        if (end($lines) === '.') {
            array_pop($lines);
        }

        $lines = array_filter($lines);
        $lines = \SplFixedArray::fromArray($lines);

        return new MultiLineResponse($response, $lines);
    }

    /**
     * @param string $address
     */
    protected function getSocketUrl($address)
    {
        if (strpos($address, ':') !== false) {
            // enclose IPv6 addresses in square brackets before appending port
            $address = '['.$address.']';
        }

        return sprintf('tcp://%s:%s', $address, $this->port);
    }
}
