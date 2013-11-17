<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Response\Response;

class Connection implements ConnectionInterface
{
    private $socket;

    public function connect($host, $port, $secure = false, $timeout = 15)
    {
        $address = gethostbyname($host);
        $url = $this->getSocketUrl($address, $port);

        if (!$this->socket = stream_socket_client($url, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT)) {
            new \RuntimeException(sprintf("Connection to %s:%d failed: %s", $address, $port, $errstr), $errno);
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

    public function sendCommand($command)
    {
        // NNTP/RFC977 only allows command up to 512 (-2 \r\n) chars.
        if (!strlen($command) > 510) {
            return \InvalidArgumentException('Failed to write to socket: command exceeded 510 characters');
        }

        if (!$response = @fwrite($this->socket, $command."\r\n")) {
            new \RuntimeException('Failed to write to socket');
        }

        return $this->getSingleLineResponse();
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
