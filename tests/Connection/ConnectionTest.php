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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Exception\SocketException;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;
use Rvdv\Nntp\Socket\SocketInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ConnectionTest extends TestCase
{
    /**
     * @var MockObject&SocketInterface
     */
    private $socket;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->socket = $this->createMock(SocketInterface::class);

        $this->socket->expects($this->any())
            ->method('connect')
            ->with('tcp://localhost:5000')
            ->willReturnSelf();
    }

    public function testConnectionCanBeEstablishedThroughSocket(): void
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

    public function testConnectionCanBeEstablishedWithConnectTimeoutAndCustomSocket(): void
    {
        $this->socket->expects($this->atLeastOnce())
            ->method('eof')
            ->willReturn(false);

        $this->socket->expects($this->once())
            ->method('connect')
            ->with('tcp://localhost:5000')
            ->willReturnSelf();

        $this->socket->expects($this->once())
            ->method('gets')
            ->with(1024)
            ->willReturn("200 server ready - posting allowed\r\n");

        $connection = new Connection('localhost', 5000, false, $this->socket, 5.0);

        $response = $connection->connect();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

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
    public function testConnectionRejectsInvalidConnectTimeout(float $connectTimeout): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\InvalidArgumentException::class);

        new Connection('localhost', 5000, connectTimeout: $connectTimeout);
    }

    public function testConnectionCanBeEncrypted(): void
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

    public function testErrorIsThrownWhenConnectionCannotBeEstablished(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $this->socket->expects($this->once())
            ->method('connect')
            ->with('tcp://localhost:5000')
            ->willThrowException(new SocketException());

        $connection = new Connection('localhost', 5000, true, $this->socket);
        $connection->connect();
    }

    public function testConnectionCanBeDisconnected(): void
    {
        $this->socket->expects($this->once())
            ->method('disconnect');

        $connection = new Connection('localhost', 5000, false, $this->socket);
        $connection->disconnect();
    }

    public function testConnectionCallsCorrespondingHandlerWhenSendingCommand(): void
    {
        $result = 'result';

        $command = new class ($result) implements CommandInterface {
            public int $invocations = 0;
            public int $multiLineChecks = 0;
            public int $handlerCalls = 0;

            public function __construct(private readonly string $result)
            {
            }

            public function __invoke(): string
            {
                ++$this->invocations;

                return 'command';
            }

            public function isMultiLine(): bool
            {
                ++$this->multiLineChecks;

                return false;
            }

            public function isCompressed(): bool
            {
                return false;
            }

            public function onPostingAllowed(ResponseInterface $response): string
            {
                ++$this->handlerCalls;

                return $this->result;
            }
        };

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
        $this->assertSame(1, $command->invocations);
        $this->assertSame(1, $command->multiLineChecks);
        $this->assertSame(1, $command->handlerCalls);
    }

    public function testSendingCommandFailsWhenCommandStringExceedsMaximumCharacters(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\InvalidArgumentException::class);

        $command = $this->createMock(CommandInterface::class);

        $command->expects($this->once())
            ->method('__invoke')
            ->willReturn(str_pad('', 512));

        $connection = new Connection('localhost', 5000, false, $this->socket);
        $connection->sendCommand($command);
    }

    public function testSendingCommandFailsWhenCommandStringIsNotSameAsSocketOutput(): void
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        $command = $this->createMock(CommandInterface::class);

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

    public function testSendingArticleAppendsFinalTerminator(): void
    {
        $command = new class () implements CommandInterface {
            public function __invoke(): string
            {
                return "From: from <user@example.com>\r\n\r\n..body";
            }

            public function isMultiLine(): bool
            {
                return false;
            }

            public function isCompressed(): bool
            {
                return false;
            }

            public function onArticleReceived(ResponseInterface $response): ResponseInterface
            {
                return $response;
            }
        };

        $this->socket->expects($this->atLeastOnce())
            ->method('eof')
            ->willReturn(false);

        $this->socket->expects($this->once())
            ->method('gets')
            ->with(1024)
            ->willReturn("240 article received ok\r\n");

        $expectedWrite = "From: from <user@example.com>\r\n\r\n..body\r\n.\r\n";

        $this->socket->expects($this->once())
            ->method('write')
            ->with($expectedWrite)
            ->willReturn(strlen($expectedWrite));

        $connection = new Connection('localhost', 5000, false, $this->socket);

        $response = $connection->sendArticle($command);

        $this->assertSame(Response::$codes['ArticleReceived'], $response->getStatusCode());
    }
}
