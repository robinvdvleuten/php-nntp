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
    private $stream;

    /**
     * {@inheritdoc}
     */
    public function setBlocking($blocking)
    {
        if (!stream_set_blocking($this->stream, $blocking ? 1 : 0)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setReadBuffer($buffer)
    {
        if (stream_set_read_buffer($this->stream, $buffer) !== 0) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enableCrypto($enable, $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT)
    {
        if (!stream_socket_enable_crypto($this->stream, $enable, $cryptoType)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function connect($address, $timeout = null)
    {
        if (!$this->stream = stream_socket_client($address, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT)) {
            throw new SocketException(sprintf('Connection to %s failed: %s', $address, $errstr));
        }

        // Use unbuffered read operations on the underlying stream resource.
        // Reading chunks from the stream may otherwise leave unread bytes in
        // PHP's stream buffers which some event loop implementations do not
        // trigger events on (edge triggered).
        // This does not affect the default event loop implementation (level
        // triggered), so we can ignore platforms not supporting this (HHVM).
        $this->setReadBuffer(0);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if (!fclose($this->stream)) {
            throw new SocketException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * {@inheritdoc}
     */
    public function gets($length = null)
    {
        if (($data = fgets($this->stream, $length)) === false) {
            throw new SocketException();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (($data = fread($this->stream, $length)) === false) {
            throw new SocketException();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (($bytes = fwrite($this->stream, $data)) === false) {
            throw new SocketException();
        }

        return $bytes;
    }
}
