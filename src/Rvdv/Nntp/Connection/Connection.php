<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

class Connection implements ConnectionInterface
{
    /**
     * @var int
     */
    private $bufferSize = 256;

    private $socket;

    public function connect($host, $port, $secure = false, $timeout = 15)
    {
        $address = gethostbyname($host);
        $url = $this->getSocketUrl($address, $port);

        if (!$this->socket = stream_socket_client($url, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT)) {
            new \RuntimeException(sprintf('Connection to %s:%d failed: %s', $address, $port, $errstr), $errno);
        }

        if ($secure) {
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }

        stream_set_blocking($this->socket, 0);

        return $this->getResponse();
    }

    public function disconnect()
    {
        if (is_resource($this->socket)) {
            stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
            return fclose($this->socket);
        }

        return false;
    }

    public function sendCommand(CommandInterface $command)
    {
        $commandString = $command->execute();

        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($commandString) > 510) {
            return \InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (!@fwrite($this->socket, $commandString."\r\n")) {
            new \RuntimeException('Failed to write to socket');
        }

        $response = $this->getResponse($command->isMultiLine());
        $responseHandlers = $command->getResponseHandlers();

        // Check if we received a response expected by the command.
        if (!isset($responseHandlers[$response->getStatusCode()])) {
            throw new \RuntimeException(sprintf(
                'Unexpected response received: [%d] %s',
                $response->getStatusCode(),
                $response->getMessage()
            ));
        }

        $responseHandler = $responseHandlers[$response->getStatusCode()];

        if (!is_callable(array($command, $responseHandler))) {
            throw new \RuntimeException(sprintf('Response handler (%s) is not callable method on given command object', $responseHandler));
        }

        $command->setResponse($response);
        $command->$responseHandler($response);

        return $command;
    }

    protected function getResponse($multiLine = false)
    {
        $buffer = "";
        $response = null;

        while(!feof($this->socket)) {
            $buffer .= @fread($this->socket, $this->bufferSize);

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

    protected function getSocketUrl($host, $port)
    {
        if (strpos($host, ':') !== false) {
            // enclose IPv6 addresses in square brackets before appending port
            $host = '[' . $host . ']';
        }

        return sprintf('tcp://%s:%s', $host, $port);
    }
}
