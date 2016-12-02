<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Exception\InvalidArgumentException;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Exception\UnknownHandlerException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;
use Rvdv\Nntp\Socket\Socket;
use Rvdv\Nntp\Socket\SocketInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class Connection implements ConnectionInterface
{
    const BUFFER_SIZE = 1024;

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
     * @var SocketInterface
     */
    private $socket;

    /**
     * Constructor.
     *
     * @param string          $host   the host of the NNTP server
     * @param int             $port   the port of the NNTP server
     * @param bool            $secure a bool indicating if a secure connection should be established
     * @param SocketInterface $socket an optional socket wrapper instance
     */
    public function __construct($host, $port, $secure = false, SocketInterface $socket = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->socket = $socket ?: new Socket();
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $this->socket->connect(sprintf('tcp://%s:%d', $this->host, $this->port));

        if ($this->secure) {
            $this->socket->enableCrypto(true);
        }

        return $this->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        $this->socket->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        $commandString = $command();

        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($commandString) > 510) {
            throw new InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (strlen($commandString."\r\n") !== $this->socket->write($commandString."\r\n")) {
            throw new RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse();

        if ($command->isMultiLine() && ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 399)) {
            $response = $command->isCompressed() ? $this->getCompressedResponse($response) : $this->getMultiLineResponse($response);
        }

        return $this->callCommandHandlerForResponse($command, $response);
    }

    public function sendArticle(CommandInterface $command)
    {
        $commandString = $command->execute();

        if (strlen($commandString."\r\n.\r\n") !== $this->socket->write($commandString."\r\n.\r\n")) {
            throw new RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse();

        return $this->callCommandHandlerForResponse($command, $response);
    }

    private function getResponse()
    {
        $buffer = '';

        while (!$this->socket->eof()) {
            $buffer .= $this->socket->gets(self::BUFFER_SIZE);

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

    private function getMultiLineResponse(Response $response)
    {
        $lines = [];

        while (!$this->socket->eof()) {
            $line = $this->socket->gets(self::BUFFER_SIZE);
            if (substr($line, -2) !== "\r\n" || strlen($line) < 2) {
                continue;
            }

            // Remove CR LF from the end of the line.
            $line = substr($line, 0, -2);

            // Check if the line terminates the text response.
            if ($line === '.') {
                return new MultiLineResponse($response, array_filter($lines));
            }

            // If 1st char is '.' it's doubled (NNTP/RFC977 2.4.1).
            if (substr($line, 0, 2) === '..') {
                $line = substr($line, 1);
            }

            // Add the line to the array of lines.
            $lines[] = $line;
        }
    }

    private function getCompressedResponse(Response $response)
    {
        // Determine encoding by fetching first line.
        $line = $this->socket->gets(self::BUFFER_SIZE);

        if (substr($line, 0, 7) == '=ybegin') {
            $this->disconnect();
            throw new RuntimeException('yEnc encoded overviews are not currently supported.');
        }

        $uncompressed = '';

        while (!$this->socket->eof()) {
            $buffer = $this->socket->gets(self::BUFFER_SIZE);

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

    private function callCommandHandlerForResponse(CommandInterface $command, ResponseInterface $response)
    {
        if (in_array($response->getStatusCode(), [Response::$codes['CommandUnknown'], Response::$codes['CommandUnavailable']])) {
            throw new RuntimeException('Sent command is either unknown or unavailable on server');
        }

        // Check if we received a response code that we're aware of.
        if (($responseName = array_search($response->getStatusCode(), Response::$codes, true)) === false) {
            throw new RuntimeException(sprintf(
                'Unexpected response received: [%d] %s',
                $response->getStatusCode(),
                $response->getMessage()
            ));
        }

        $responseHandlerMethod = 'on'.$responseName;

        if (!is_callable([$command, $responseHandlerMethod])) {
            throw new UnknownHandlerException(sprintf('Response handler (%s) is not a callable method on given command object', $responseHandlerMethod));
        }

        return call_user_func([$command, $responseHandlerMethod], $response);
    }
}
