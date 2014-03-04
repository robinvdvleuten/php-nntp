<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Exception\InvalidArgumentException;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * Connection
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Connection implements ConnectionInterface
{
    /**
     * @var int
     */
    private $bufferSize = 1024;

    /**
     * @var resource
     */
    private $socket;

    /**
     * Constructor
     *
     * @param string $host    The hostname of the NNTP server.
     * @param int    $port    The port of the NNTP server.
     * @param bool   $secure  A bool indicating if a secure connection should be established.
     * @param int    $timeout The socket timeout in seconds.
     */
    public function __construct($host, $port, $secure = false, $timeout = 15)
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        $address = gethostbyname($this->host);
        $url = $this->getSocketUrl($address);

        if (!$this->socket = @stream_socket_client($url, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT)) {
            throw new RuntimeException(sprintf('Connection to %s:%d failed: %s', $address, $this->port, $errstr), $errno);
        }

        if ($this->secure) {
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }

        stream_set_blocking($this->socket, 0);

        return $this->getResponse();
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        if (is_resource($this->socket)) {
            stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);

            return fclose($this->socket);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        $commandString = $command->execute();

        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($commandString) > 510) {
            throw new InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (!@fwrite($this->socket, $commandString."\r\n")) {
            throw new RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse($command->isMultiLine());
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

    protected function getResponse($multiLine = false)
    {
        $buffer = "";
        $response = null;

        while (!feof($this->socket)) {
            $buffer .= @fgets($this->socket, $this->bufferSize);

            if (!$response && substr($buffer, -2) === "\r\n") {
                $response = Response::createFromString($buffer);

                $lines = explode("\r\n", trim($buffer));
                if (count($lines) > 1) {
                    $buffer = implode("\r\n", array_slice($lines, 1))."\r\n";
                } else {
                    $buffer = "";
                }

                if (!$multiLine) {
                    return $response;
                }
            }

            if ($response && substr($buffer, -3) === ".\r\n") {
                if (substr($response->getMessage(), -15) === '[COMPRESS=GZIP]') {
                    $buffer = @gzuncompress($buffer);
                }

                $lines = explode("\r\n", trim($buffer));
                if (end($lines) === ".") {
                    array_pop($lines);
                }

                $lines = array_filter($lines);
                $lines = \SplFixedArray::fromArray($lines);

                return new MultiLineResponse($response, $lines);
            }
        }
    }

    protected function getSocketUrl($address)
    {
        if (strpos($address, ':') !== false) {
            // enclose IPv6 addresses in square brackets before appending port
            $address = '[' . $address . ']';
        }

        return sprintf('tcp://%s:%s', $address, $this->port);
    }
}
