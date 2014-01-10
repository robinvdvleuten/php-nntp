<?php

namespace Rvdv\Nntp\Tests;

use Rvdv\Nntp\Client;

/**
 * ClientTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testItConnectsWithANntpServer()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->returnValue($response));

        $client = new Client($connection);

        $this->assertSame($client->connect(), $response);
    }

    public function testItDisconnectsFromAnEstablishedConnection()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('disconnect')
            ->will($this->returnValue(true));

        $client = new Client($connection);

        $this->assertTrue($client->disconnect());
    }

    public function testItErrorsWhenDisconnectFails()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('disconnect')
            ->will($this->returnValue(false));

        $client = new Client($connection);

        try {
            $client->disconnect();
            $this->fail('->disconnect() throws a Rvdv\Nntp\Exception\RuntimeException if the established connection cannot be disconnected');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->disconnect() throws a Rvdv\Nntp\Exception\RuntimeException if the established connection cannot be disconnected');
        }
    }

    public function testItReturnsCommandInstanceWhenCallingShortcut()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->any())
            ->method('sendCommand')
            ->will($this->returnArgument(0));

        $client = new Client($connection);

        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->authInfo('USER', 'user'));
        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->group('php.doc'));
        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->overview(1, 1, array()));
        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->overviewFormat());
        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->quit());
        $this->assertInstanceOf('Rvdv\Nntp\Command\CommandInterface', $client->xfeature('COMPRESS GZIP'));
    }
}
