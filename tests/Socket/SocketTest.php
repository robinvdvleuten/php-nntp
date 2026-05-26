<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Socket;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Socket\Socket;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class SocketTest extends TestCase
{
    /**
     * @return array<int, array{float}>
     */
    public static function getInvalidConnectTimeouts(): array
    {
        return [
            [0.0],
            [-1.0],
        ];
    }

    #[DataProvider('getInvalidConnectTimeouts')]
    public function testItRejectsInvalidConnectTimeout(float $connectTimeout): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\InvalidArgumentException::class);

        new Socket($connectTimeout);
    }

    public function testItCanReadAndWriteThroughLocalSocket(): void
    {
        $server = @stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        if (false === $server) {
            $this->markTestSkipped(sprintf('Could not start local socket server: %s', $errstr));
        }

        $connection = null;
        $socket = new Socket();

        try {
            $address = stream_socket_get_name($server, false);
            $this->assertIsString($address);

            $this->assertSame($socket, $socket->connect($address));

            $connection = stream_socket_accept($server, 1);
            $this->assertIsResource($connection);

            $data = "PING\r\n";
            $this->assertSame(strlen($data), $socket->write($data));
            $this->assertSame($data, fread($connection, strlen($data)));

            $response = "PONG\r\n";
            $this->assertSame(strlen($response), fwrite($connection, $response));
            $this->assertSame('PONG', $socket->read(4));

            $this->assertSame($socket, $socket->disconnect());
        } finally {
            if (is_resource($connection)) {
                fclose($connection);
            }

            fclose($server);
        }
    }

    public function testConnectFail(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\SocketException::class);

        $socket = new Socket();
        $socket->connect('localhost:2');
    }
}
