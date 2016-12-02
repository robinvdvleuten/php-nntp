<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Exception\SocketException;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Socket\Socket;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $socket;

    /**
     * {@inheritdoc}
     */
    public function setup()
    {
        $this->socket = $this->getMockBuilder(Socket::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->socket->expects($this->any())
            ->method('connect')
            ->with('tcp://localhost:5000')
            ->willReturnSelf();
    }

    public function testConnectionCanBeEstablishedThroughSocket()
    {
        $this->socket->expects($this->atLeastOnce())
            ->method('eof')
            ->willReturn(false);

        $this->socket->expects($this->once())
            ->method('gets')
            ->with(1024)
            ->willReturn("200 server ready - posting allowed\r\n");

        $connection = new Connection('localhost', 5000, false, $this->socket);

        $response = $connection->connect();

        $this->assertInstanceof(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
    }

    public function testConnectionCanBeEncrypted()
    {
        $this->socket->expects($this->atLeastOnce())
            ->method('eof')
            ->willReturn(false);

        $this->socket->expects($this->once())
            ->method('gets')
            ->with(1024)
            ->willReturn("200 server ready - posting allowed\r\n");

        $this->socket->expects($this->once())
            ->method('enableCrypto')
            ->with(true);

        $connection = new Connection('localhost', 5000, true, $this->socket);

        $response = $connection->connect();

        $this->assertInstanceof(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testErrorIsThrownWhenConnectionCannotBeEstablished()
    {
        $this->socket->expects($this->once())
            ->method('connect')
            ->with('tcp://localhost:5000')
            ->willThrowException(new SocketException());

        $connection = new Connection('localhost', 5000, true, $this->socket);
        $connection->connect();
    }

    public function testConnectionCanBeDisconnected()
    {
        $this->socket->expects($this->once())
            ->method('disconnect');

        $connection = new Connection('localhost', 5000, false, $this->socket);
        $connection->disconnect();
    }

    public function testConnectionCallsCorrespondingHandlerWhenSendingCommand()
    {
        $result = 'result';

        $command = $this->getMock(CommandInterface::class, [
            '__invoke', 'isMultiLine', 'isCompressed', 'onPostingAllowed'
        ]);

        $command->expects($this->once())
            ->method('__invoke')
            ->willReturn('command');

        $command->expects($this->once())
            ->method('isMultiLine')
            ->willReturn(false);

        $command->expects($this->once())
            ->method('onPostingAllowed')
            ->willReturn($result);

        $this->socket->expects($this->atLeastOnce())
            ->method('eof')
            ->willReturn(false);

        $this->socket->expects($this->once())
            ->method('gets')
            ->with(1024)
            ->willReturn("200 command received - go ahead\r\n");

        $this->socket->expects($this->once())
            ->method('write')
            ->with("command\r\n")
            ->willReturn(9);

        $connection = new Connection('localhost', 5000, false, $this->socket);

        $this->assertSame($result, $connection->sendCommand($command));
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\InvalidArgumentException
     */
    public function testSendingCommandFailsWhenCommandStringExceedsMaximumCharacters()
    {
        $command = $this->getMock(CommandInterface::class);

        $command->expects($this->once())
            ->method('__invoke')
            ->willReturn(str_pad('', 512));

        $connection = new Connection('localhost', 5000, false, $this->socket);
        $connection->sendCommand($command);
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testSendingCommandFailsWhenCommandStringIsNotSameAsSocketOutput()
    {
        $command = $this->getMock(CommandInterface::class);

        $command->expects($this->once())
            ->method('__invoke')
            ->willReturn('command');

        $this->socket->expects($this->once())
            ->method('write')
            ->with("command\r\n")
            ->willReturn(0);

        $connection = new Connection('localhost', 5000, false, $this->socket);
        $connection->sendCommand($command);
    }
}
