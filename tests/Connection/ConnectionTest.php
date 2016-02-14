<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Connection;

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Exception\RuntimeException;
use Socket\Raw\Exception;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $socket;

    /**
     * {@inheritdoc}
     */
    public function setup()
    {
        $this->socket = $this->getMockBuilder('Socket\Raw\Socket')
            ->disableOriginalConstructor()
            ->getMock();

        $this->socket->expects($this->once())
            ->method('connectTimeout')
            ->with('localhost:5000', 15)
            ->willReturnSelf();

        $this->factory = $this->getMock('Socket\Raw\Factory');

        $this->factory->expects($this->once())
            ->method('createFromString')
            ->with('localhost:5000')
            ->willReturn($this->socket);

        $this->connection = new Connection('localhost:5000', false, 15, $this->factory);
    }

    public function testConnectionCanBeEstablishedThroughSocket()
    {
        $this->socket->expects($this->atLeastOnce())
            ->method('selectRead')
            ->with(15)
            ->willReturn(true);

        $this->socket->expects($this->once())
            ->method('read')
            ->with(1024)
            ->willReturn("200 server ready - posting allowed\r\n");

        $response = $this->connection->connect();

        $this->assertInstanceof('Rvdv\Nntp\Response\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testErrorIsThrownWhenConnectionCannotBeEstablished()
    {
        $this->socket->expects($this->once())
            ->method('connectTimeout')
            ->with('localhost:5000', 15)
            ->willThrowException(new Exception());

        $this->connection->connect();
    }

    /**
     * {@inheritdoc}
     */
    public function teardown()
    {
        unset($this->factory, $this->socket);
    }
}
