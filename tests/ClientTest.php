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
use Rvdv\Nntp\Command;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnsConnectionInstance()
    {
        $connection = $this->getMock(ConnectionInterface::class);

        $client = new Client($connection);

        $this->assertSame($client->getConnection(), $connection);
    }

    public function testItConnectsWithAServer()
    {
        $response = $this->getMock(ResponseInterface::class);

        $connection = $this->getMock(ConnectionInterface::class, [
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
        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('disconnect');

        $client = new Client($connection);
        $client->disconnect();
    }

    public function testItAuthenticatesUsernameWithConnectedServer()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(Response::$codes['AuthenticationAccepted']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf(Command\AuthInfoCommand::class),
                $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'username')
            ))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->authenticate('username');
    }

    public function testItAuthenticatesUsernamePasswordWithConnectedServer()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::$codes['PasswordRequired'], Response::$codes['AuthenticationAccepted']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->exactly(2))
            ->method('sendCommand')
            ->withConsecutive(
                [$this->logicalAnd(
                    $this->isInstanceOf(Command\AuthInfoCommand::class),
                    $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                    $this->attributeEqualTo('value', 'username')
                )],
                [$this->logicalAnd(
                    $this->isInstanceOf(Command\AuthInfoCommand::class),
                    $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_PASS),
                    $this->attributeEqualTo('value', 'password')
                )]
            )
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->authenticate('username', 'password');
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testItErrorsWhenAuthenticateNeedsPassword()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(Response::$codes['PasswordRequired']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf(Command\AuthInfoCommand::class),
                $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'username')
            ))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->authenticate('username');
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testItErrorsWhenAuthenticateFails()
    {
        $response = $this->getMock('Rvdv\Nntp\Response\ResponseInterface');

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(Response::$codes['AuthenticationRejected']));

        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand'),
                $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'unknown')
            ))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->authenticate('unknown');
    }

    public function testItConnectsAndAuthenticatesUsernameWithConnectedServer()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(3))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::$codes['PostingAllowed'], Response::$codes['AuthenticationAccepted'], Response::$codes['AuthenticationAccepted']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->logicalAnd(
                $this->isInstanceOf(Command\AuthInfoCommand::class),
                $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                $this->attributeEqualTo('value', 'username')
            ))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->connectAndAuthenticate('username');
    }

    public function testItConnectsAndAuthenticatesUsernamePasswordWithConnectedServer()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(3))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::$codes['PostingAllowed'], Response::$codes['PasswordRequired'], Response::$codes['AuthenticationAccepted']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->exactly(2))
            ->method('sendCommand')
            ->withConsecutive(
                [$this->logicalAnd(
                    $this->isInstanceOf(Command\AuthInfoCommand::class),
                    $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_USER),
                    $this->attributeEqualTo('value', 'username')
                )],
                [$this->logicalAnd(
                    $this->isInstanceOf(Command\AuthInfoCommand::class),
                    $this->attributeEqualTo('type', Command\AuthInfoCommand::AUTHINFO_PASS),
                    $this->attributeEqualTo('value', 'password')
                )]
            )
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->connectAndAuthenticate('username', 'password');
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testItErrorsWhenConnectingFails()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(null));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->never())
            ->method('sendCommand');

        $client = new Client($connection);
        $client->connectAndAuthenticate('username');
    }

    public function testItReturnResponseWhenPostingAnArticle()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::$codes['SendArticle'], Response::$codes['ArticleReceived']));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->isInstanceOf(Command\PostCommand::class))
            ->will($this->returnValue($response));

        $connection->expects($this->once())
            ->method('sendArticle')
            ->with($this->isInstanceOf(Command\PostArticleCommand::class))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->post('php.doc', 'A very important article', 'Read more in the body', 'johndoe@example.com');
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testItErrorsWhenPostingFails()
    {
        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->onConsecutiveCalls(Response::$codes['SendArticle'], null));

        $connection = $this->getMock(ConnectionInterface::class, [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->isInstanceOf(Command\PostCommand::class))
            ->will($this->returnValue($response));

        $connection->expects($this->once())
            ->method('sendArticle')
            ->with($this->isInstanceOf(Command\PostArticleCommand::class))
            ->will($this->returnValue($response));

        $client = new Client($connection);
        $client->post('php.doc', 'A very important article', 'Read more in the body', 'johndoe@example.com');
    }

    /**
     * @return array
     */
    public function getClassesForCommandMethods()
    {
        return [
            [Command\AuthInfoCommand::class, 'authInfo', ['USER', 'user']],
            [Command\BodyCommand::class, 'body', ['1234']],
            [Command\HeadCommand::class, 'head', ['12345']],
            [Command\GroupCommand::class, 'group', ['php.doc']],
            [Command\HelpCommand::class, 'help', []],
            [Command\ListCommand::class, 'listGroups', ['php', []]],
            [Command\OverviewFormatCommand::class, 'overviewFormat', []],
            [Command\PostArticleCommand::class, 'postArticle', ['php.doc', 'A very important article', 'Read more in the body', 'johndoe@example.com', 'headers']],
            [Command\QuitCommand::class, 'quit', []],
            [Command\XFeatureCommand::class, 'xfeature', ['COMPRESS GZIP']],
            [Command\XoverCommand::class, 'xover', [1, 1, []]],
            [Command\XzverCommand::class, 'xzver', [1, 1, []]],
        ];
    }

    /**
     * @dataProvider getClassesForCommandMethods
     */
    public function testItReturnsResultOfCommandWhenCallingMethod($commandClass, $method, array $arguments)
    {
        $connection = $this->getMock('Rvdv\Nntp\Connection\ConnectionInterface', [
            'connect', 'disconnect', 'sendCommand', 'sendArticle',
        ]);

        $connection->expects($this->any())
            ->method('sendCommand')
            ->will($this->returnArgument(0));

        $connection->expects($this->any())
            ->method('sendArticle')
            ->will($this->returnArgument(0));

        $client = new Client($connection);

        $this->assertInstanceOf($commandClass, call_user_func_array([$client, $method], $arguments));
    }
}
