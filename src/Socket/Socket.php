<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Socket;

use Rvdv\Nntp\Exception\InvalidArgumentException;
use Rvdv\Nntp\Exception\SocketException;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class Socket implements SocketInterface
{
    private float $connectTimeout;

    /**
     * @var resource
     */
    private $stream;

    public function __construct(float $connectTimeout = 1.0)
    {
        if ($connectTimeout <= 0) {
            throw new InvalidArgumentException('Connect timeout must be greater than 0 seconds');
        }

        $this->connectTimeout = $connectTimeout;
    }

    public function enableCrypto(bool $enable, int $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT): self
    {
        if (!stream_socket_enable_crypto($this->stream, $enable, $cryptoType)) {
            throw new SocketException();
        }

        return $this;
    }

    public function connect(string $address): self
    {
        $stream = @stream_socket_client($address, $errno, $errstr, $this->connectTimeout, STREAM_CLIENT_CONNECT);
        if (false === $stream) {
            throw new SocketException(sprintf('Connection to %s failed: %s', $address, $errstr));
        }

        $this->stream = $stream;

        stream_set_blocking($this->stream, true);

        // Use unbuffered read operations on the underlying stream resource.
        // Reading chunks from the stream may otherwise leave unread bytes in
        // PHP's stream buffers which some event loop implementations do not
        // trigger events on (edge triggered).
        // This does not affect the default event loop implementation (level
        // triggered), so we can ignore platforms not supporting this (HHVM).
        if (function_exists('stream_set_read_buffer')) {
            stream_set_read_buffer($this->stream, 0);
        }

        return $this;
    }

    public function disconnect(): self
    {
        if (!fclose($this->stream)) {
            throw new SocketException();
        }

        return $this;
    }

    public function eof(): bool
    {
        return feof($this->stream);
    }

    public function gets(?int $length = null): string
    {
        if (null !== $length && $length < 1) {
            throw new SocketException();
        }

        if (false === ($data = fgets($this->stream, $length))) {
            throw new SocketException();
        }

        return $data;
    }

    public function read(int $length): string
    {
        if ($length < 1) {
            throw new SocketException();
        }

        if (false === ($data = fread($this->stream, $length))) {
            throw new SocketException();
        }

        return $data;
    }

    public function write(string $data): int
    {
        if (false === ($bytes = fwrite($this->stream, $data))) {
            throw new SocketException();
        }

        return $bytes;
    }
}
