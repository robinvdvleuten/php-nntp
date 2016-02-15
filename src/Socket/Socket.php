<?php

namespace Rvdv\Nntp\Socket;

use Rvdv\Nntp\Exception\SocketException;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Socket implements SocketInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * {@inheritdoc}
     */
    public function setBlocking($blocking)
    {
        if (!stream_set_blocking($this->resource, $blocking ? 1 : 0)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enableCrypto($enable, $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT)
    {
        if (!stream_socket_enable_crypto($this->resource, $enable, $cryptoType)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function connect($address, $timeout = null)
    {
        if (!$this->resource = stream_socket_client($address, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT)) {
            throw new SocketException(sprintf('Connection to %s failed: %s', $address, $errstr));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if (!fclose($this->resource)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (($data = fread($this->resource, $length)) === false) {
            throw new SocketException();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (($bytes = fwrite($this->resource, $data)) === false) {
            throw new SocketException();
        }

        return $bytes;
    }
}
