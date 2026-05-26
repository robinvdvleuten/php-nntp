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

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
interface SocketInterface
{
    public function enableCrypto(bool $enable, int $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT): self;

    public function connect(string $address): self;

    public function disconnect(): self;

    public function eof(): bool;

    public function gets(?int $length = null): string;

    public function read(int $length): string;

    public function write(string $data): int;
}
