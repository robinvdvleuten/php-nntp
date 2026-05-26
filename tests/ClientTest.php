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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rvdv\Nntp\Client;
use Rvdv\Nntp\Command;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ClientTest extends TestCase
{
    public function testItReturnsConnectionInstance(): void
    {
        $connection = $this->createMock(ConnectionInterface::class);

        $client = new Client($connection);

        $this->assertSame($client->getConnection(), $connection);
    }

    public function testItConnectsWithAServer(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $client = new Client($connection);

        $this->assertSame($client->connect(), $response);
    }

    public function testItDisconnectsFromAnEstablishedConnection(): void
    {
        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('disconnect');

        $client = new Client($connection);
        $client->disconnect();
    }

    public function testItAuthenticatesUsernameWithConnectedServer(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['AuthenticationAccepted']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) {
                return 'AUTHINFO USER username' === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->authenticate('username');
    }

    public function testItAuthenticatesUsernamePasswordWithConnectedServer(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['PasswordRequired'], Response::$codes['AuthenticationAccepted']);

        $expectedCommands = ['AUTHINFO USER username', 'AUTHINFO PASS password'];

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->exactly(2))
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) use (&$expectedCommands) {
                return array_shift($expectedCommands) === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->authenticate('username', 'password');
    }

    public function testItErrorsWhenAuthenticateNeedsPassword(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::$codes['PasswordRequired']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) {
                return 'AUTHINFO USER username' === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->authenticate('username');
    }

    public function testItErrorsWhenAuthenticateFails(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['AuthenticationRejected']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) {
                return 'AUTHINFO USER unknown' === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->authenticate('unknown');
    }

    public function testItConnectsAndAuthenticatesUsernameWithConnectedServer(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(3))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['PostingAllowed'], Response::$codes['AuthenticationAccepted'], Response::$codes['AuthenticationAccepted']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) {
                return 'AUTHINFO USER username' === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->connectAndAuthenticate('username');
    }

    public function testItConnectsAndAuthenticatesUsernamePasswordWithConnectedServer(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(3))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['PostingAllowed'], Response::$codes['PasswordRequired'], Response::$codes['AuthenticationAccepted']);

        $expectedCommands = ['AUTHINFO USER username', 'AUTHINFO PASS password'];

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->exactly(2))
            ->method('sendCommand')
            ->with($this->callback(function (Command\AuthInfoCommand $command) use (&$expectedCommands) {
                return array_shift($expectedCommands) === $command();
            }))
            ->willReturn($response);

        $client = new Client($connection);
        $client->connectAndAuthenticate('username', 'password');
    }

    public function testItErrorsWhenConnectingFails(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::$codes['CommandUnavailable']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('connect')
            ->willReturn($response);

        $connection->expects($this->never())
            ->method('sendCommand');

        $client = new Client($connection);
        $client->connectAndAuthenticate('username');
    }

    public function testItReturnResponseWhenPostingAnArticle(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['SendArticle'], Response::$codes['ArticleReceived']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->isInstanceOf(Command\PostCommand::class))
            ->willReturn($response);

        $connection->expects($this->once())
            ->method('sendArticle')
            ->with($this->isInstanceOf(Command\PostArticleCommand::class))
            ->willReturn($response);

        $client = new Client($connection);
        $client->post('php.doc', 'A very important article', 'Read more in the body', 'johndoe@example.com');
    }

    public function testItErrorsWhenPostingFails(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::$codes['SendArticle'], Response::$codes['PostingFailed']);

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->once())
            ->method('sendCommand')
            ->with($this->isInstanceOf(Command\PostCommand::class))
            ->willReturn($response);

        $connection->expects($this->once())
            ->method('sendArticle')
            ->with($this->isInstanceOf(Command\PostArticleCommand::class))
            ->willReturn($response);

        $client = new Client($connection);
        $client->post('php.doc', 'A very important article', 'Read more in the body', 'johndoe@example.com');
    }

    /**
     * @return array<int, array{class-string, string, array<int, mixed>}>
     */
    public static function getClassesForCommandMethods(): array
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
     * @param class-string      $commandClass
     * @param array<int, mixed> $arguments
     */
    #[DataProvider('getClassesForCommandMethods')]
    public function testItReturnsResultOfCommandWhenCallingMethod(string $commandClass, string $method, array $arguments): void
    {
        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects($this->any())
            ->method('sendCommand')
            ->willReturnArgument(0);

        $connection->expects($this->any())
            ->method('sendArticle')
            ->willReturnArgument(0);

        $client = new Client($connection);

        $this->assertTrue(method_exists($client, $method));
        $this->assertInstanceOf($commandClass, $client->{$method}(...$arguments));
    }
}
