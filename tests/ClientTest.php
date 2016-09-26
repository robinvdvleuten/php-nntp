<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests;

use Rvdv\Nntp\Client;
use Rvdv\Nntp\Command\AuthInfoCommand;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnsConnectionInstance()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface');

        $client = new Client($connection);

        $this->assertSame($client->getConnection(), $connection);
    }

    public function testItConnectsWithANntpServer()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->returnValue($response));

        $client = new Client($connection);

        $this->assertSame($client->connect(), $response);
    }

    public function testItDisconnectsFromAnEstablishedConnection()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('disconnect');

        $client = new Client($connection);
        $client->disconnect();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testItErrorsWhenDisconnectFails()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('disconnect')
            ->willThrowException(new RuntimeException());

        $client = new Client($connection);
        $client->disconnect();
    }

    public function testItAuthenticatesUsernameWithConnectedServer()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(Response::AUTHENTICATION_ACCEPTED));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $command->expects($this->once())
            ->method('getResponse')
            ->will($this->returnvalue($response));

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                $this->attributeEqualTo('type', AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'username')
            ))
            ->will($this->returnValue($command));

        $client = new Client($connection);
        $client->authenticate('username');
    }

    public function testItAuthenticatesUsernamePasswordWithConnectedServer()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::PASSWORD_REQUIRED, Response::AUTHENTICATION_ACCEPTED));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $command->expects($this->exactly(2))
            ->method('getResponse')
            ->will($this->returnvalue($response));

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->exactly(2))
            ->method('sendCommand')
            ->withConsecutive(
                [$this->logicalAnd(
                    $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                    $this->attributeEqualTo('type', AuthInfoCommand::AUTHINFO_USER),
                    $this->attributeEqualTo('value', 'username')
                )],
                [$this->logicalAnd(
                    $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                    $this->attributeEqualTo('type', AuthInfoCommand::AUTHINFO_PASS),
                    $this->attributeEqualTo('value', 'password')
                )]
            )
            ->will($this->returnValue($command));

        $client = new Client($connection);
        $client->authenticate('username', 'password');
    }

    public function testItErrorsWhenAuthenticateNeedsPassword()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(Response::PASSWORD_REQUIRED));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $command->expects($this->once())
            ->method('getResponse')
            ->will($this->returnvalue($response));

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                $this->attributeEqualTo('type', AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'username')
            ))
            ->will($this->returnValue($command));

        $client = new Client($connection);

        try {
            $client->authenticate('username');
            $this->fail('->authenticate() throws a Rvdv\Nntp\Exception\RuntimeException because a password is needed but none is given');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->authenticate() throws a Rvdv\Nntp\Exception\RuntimeException because a password is needed but none is given');
        }
    }

    public function testItErrorsWhenAuthenticateFails()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(Response::AUTHENTICATION_REJECTED));

        $command = $this->getMock('Rvdv\Nntp\Command\CommandInterface');

        $command->expects($this->once())
            ->method('getResponse')
            ->will($this->returnvalue($response));

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                $this->attributeEqualTo('type', AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'unknown')
            ))
            ->will($this->returnValue($command));

        $client = new Client($connection);

        try {
            $client->authenticate('unknown');
            $this->fail('->authenticate() throws a Rvdv\Nntp\Exception\RuntimeException because incorrect credentials are given');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->authenticate() throws a Rvdv\Nntp\Exception\RuntimeException because incorrect credentials are given');
        }
    }

    public function testItReturnsCommandInstanceWhenCallingShortcut()
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->any())
            ->method('sendCommand')
            ->will($this->returnArgument(0));

        $client = new Client($connection);

        $this->assertInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand', $client->authInfo('USER', 'user'));
        $this->assertInstanceOf('Rvdv\Nntp\Command\GroupCommand', $client->group('php.doc'));
        $this->assertInstanceOf('Rvdv\Nntp\Command\HelpCommand', $client->help());
        $this->assertInstanceOf('Rvdv\Nntp\Command\OverviewFormatCommand', $client->overviewFormat());
        $this->assertInstanceOf('Rvdv\Nntp\Command\QuitCommand', $client->quit());
        $this->assertInstanceOf('Rvdv\Nntp\Command\XfeatureCommand', $client->xfeature('COMPRESS GZIP'));
        $this->assertInstanceOf('Rvdv\Nntp\Command\XoverCommand', $client->xover(1, 1, []));
        $this->assertInstanceOf('Rvdv\Nntp\Command\XzverCommand', $client->xzver(1, 1, []));
    }
}
