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
    public function testItShouldConnectWithANntpServer()
    {
        $client = new Client();

        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('news.php.net'), $this->equalTo(119))
            ->will($this->returnValue($response));

        $client->setConnection($connection);

        $this->assertSame($client->connect('news.php.net', 119), $response);
    }

    public function testItShouldDisconnectFromAnEstablishedConnection()
    {
        $client = new Client();

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('disconnect')
            ->will($this->returnValue(true));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $connection->expects($this->once())
            ->method('sendCommand')
            ->will($this->returnValue($command));

        $client->setConnection($connection);

        $this->assertSame($client->disconnect(), $command);
    }

    public function testItShouldErrorsWhenDisconnectFails()
    {
        $client = new Client();

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', array(
            'connect', 'disconnect', 'sendCommand',
        ));

        $connection->expects($this->once())
            ->method('disconnect')
            ->will($this->returnValue(false));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $connection->expects($this->once())
            ->method('sendCommand')
            ->will($this->returnValue($command));

        $client->setConnection($connection);

        try {
            $client->disconnect();
            $this->fail('->disconnect() throws a Rvdv\Nntp\Exception\RuntimeException if the established connection cannot be disconnected');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->disconnect() throws a Rvdv\Nntp\Exception\RuntimeException if the established connection cannot be disconnected');
        }
    }
}
