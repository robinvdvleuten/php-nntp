<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Response\Response;

class Connection implements ConnectionInterface
{
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

        stream_set_blocking($this->socket, 1);

        return $this->getSingleLineResponse();
    }

    public function disconnect()
    {
        return fclose($this->socket);
    }

    public function sendCommand(CommandInterface $command)
    {
        $commandString = $command->execute();

        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($commandString) > 510) {
            return \InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (!$response = @fwrite($this->socket, $commandString."\r\n")) {
            new \RuntimeException('Failed to write to socket');
        }

        $response = $this->getSingleLineResponse();
        $command->setResponse($response);

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

        return $command->$responseHandler($response);
    }

    protected function getSingleLineResponse()
    {
        if (!$response = @fgets($this->socket, 256)) {
            new \RuntimeException('Failed to read from socket');
        }

        return Response::createFromString($response);
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
