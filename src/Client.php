<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Exception\LogicException;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class Client implements ClientInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the connection instance.
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function connect(): ResponseInterface
    {
        return $this->connection->connect();
    }

    public function disconnect(): void
    {
        $this->connection->disconnect();
    }

    public function authenticate(string $username, ?string $password = null): ResponseInterface
    {
        $response = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_USER, (string) $username);

        if ($response->getStatusCode() === Response::$codes['PasswordRequired']) {
            if (null === $password) {
                throw new RuntimeException('NNTP server asks for further authentication but no password is given');
            }

            $response = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_PASS, (string) $password);
        }

        if ($response->getStatusCode() !== Response::$codes['AuthenticationAccepted']) {
            throw new RuntimeException(sprintf('Could not authenticate with given username/password: %s', (string) $response));
        }

        return $response;
    }

    public function connectAndAuthenticate(string $username, ?string $password = null): ResponseInterface
    {
        $response = $this->connect();

        if (!in_array($response->getStatusCode(), [Response::$codes['PostingAllowed'], Response::$codes['PostingProhibited']])) {
            throw new RuntimeException(sprintf('Unsuccessful response received: %s', (string) $response));
        }

        return $this->authenticate($username, $password);
    }

    public function authInfo(string $type, string $value): ResponseInterface
    {
        return $this->responseResult($this->sendCommand(new Command\AuthInfoCommand($type, $value)));
    }

    public function body(string $article): string
    {
        return $this->stringResult($this->sendCommand(new Command\BodyCommand($article)));
    }

    public function head(string $article): string
    {
        return $this->stringResult($this->sendCommand(new Command\HeadCommand($article)));
    }

    /**
     * @return array<int, array{name: string, high: string, low: string, status: string}>
     */
    public function listGroups(?string $keyword = null, mixed $arguments = null): array
    {
        return $this->arrayResult($this->sendCommand(new Command\ListCommand($keyword, $arguments)));
    }

    /**
     * @return array{count: string, first: string, last: string, name: string}
     */
    public function group(string $name): array
    {
        return $this->groupResult($this->sendCommand(new Command\GroupCommand($name)));
    }

    public function help(): string
    {
        return $this->stringResult($this->sendCommand(new Command\HelpCommand()));
    }

    /**
     * @return array<string, bool>
     */
    public function overviewFormat(): array
    {
        return $this->arrayResult($this->sendCommand(new Command\OverviewFormatCommand()));
    }

    public function post(string $groups, string $subject, string $body, string $from, ?string $headers = null): ResponseInterface
    {
        $response = $this->sendCommand(new Command\PostCommand());

        if ($response->getStatusCode() === Response::$codes['SendArticle']) {
            $response = $this->postArticle($groups, $subject, $body, $from, $headers);
        }

        if ($response->getStatusCode() !== Response::$codes['ArticleReceived']) {
            throw new RuntimeException(sprintf('Posting failed: %s', (string) $response));
        }

        return $response;
    }

    public function postArticle(string $groups, string $subject, string $body, string $from, ?string $headers = null): ResponseInterface
    {
        return $this->responseResult($this->sendArticle(new Command\PostArticleCommand($groups, $subject, $body, $from, $headers)));
    }

    public function quit(): void
    {
        $this->sendCommand(new Command\QuitCommand());
    }

    public function xfeature(string $feature): bool
    {
        return $this->boolResult($this->sendCommand(new Command\XFeatureCommand($feature)));
    }

    /**
     * @param array<string, bool> $format
     *
     * @return array<int, array<string, string>>
     */
    public function xover(int $from, int $to, array $format): array
    {
        return $this->arrayResult($this->sendCommand(new Command\XoverCommand($from, $to, $format)));
    }

    /**
     * @param array<string, bool> $format
     *
     * @return array<int, array<string, string>>
     */
    public function xzver(int $from, int $to, array $format): array
    {
        return $this->arrayResult($this->sendCommand(new Command\XzverCommand($from, $to, $format)));
    }

    public function sendCommand(CommandInterface $command): mixed
    {
        return $this->connection->sendCommand($command);
    }

    public function sendArticle(CommandInterface $command): mixed
    {
        return $this->connection->sendArticle($command);
    }

    private function responseResult(mixed $result): ResponseInterface
    {
        if (!$result instanceof ResponseInterface) {
            throw new LogicException(sprintf('Expected command result to be %s.', ResponseInterface::class));
        }

        return $result;
    }

    private function stringResult(mixed $result): string
    {
        if (!is_string($result)) {
            throw new LogicException('Expected command result to be a string.');
        }

        return $result;
    }

    /**
     * @return array<mixed>
     */
    private function arrayResult(mixed $result): array
    {
        if (!is_array($result)) {
            throw new LogicException('Expected command result to be an array.');
        }

        return $result;
    }

    private function boolResult(mixed $result): bool
    {
        if (!is_bool($result)) {
            throw new LogicException('Expected command result to be a boolean.');
        }

        return $result;
    }

    /**
     * @return array{count: string, first: string, last: string, name: string}
     */
    private function groupResult(mixed $result): array
    {
        if (
            !is_array($result)
            || !isset($result['count'], $result['first'], $result['last'], $result['name'])
            || !is_string($result['count'])
            || !is_string($result['first'])
            || !is_string($result['last'])
            || !is_string($result['name'])
        ) {
            throw new LogicException('Expected command result to be a group array.');
        }

        return $result;
    }
}
