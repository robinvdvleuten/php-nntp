<?php

namespace Rvdv\Nntp\Tests\Socket;

use Rvdv\Nntp\Socket\Socket;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class SocketTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectGoogle()
    {
        $socket = new Socket();

        $this->assertSame($socket, $socket->connect('www.google.nl:80'));

        // Send HTTP request to remote server.
        $data = "GET / HTTP/1.1\r\nHost: www.google.com\r\n\r\n";
        $this->assertSame(strlen($data), $socket->write($data));

        $this->assertSame('HTTP', $socket->read(4));

        // Expect there's more data in the socket.
        $this->assertFalse($socket->eof());

        // Read a whole chunk from socket.
        $this->assertNotEmpty($socket->read(8192));

        $this->assertSame($socket, $socket->disconnect());
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\SocketException
     */
    public function testConnectFail()
    {
        $socket = new Socket();
        $socket->connect('localhost:2');
    }
}
