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

    public function authInfo(string $type, string $value): mixed
    {
        return $this->sendCommand(new Command\AuthInfoCommand($type, $value));
    }

    public function body(string $article): mixed
    {
        return $this->sendCommand(new Command\BodyCommand($article));
    }

    public function head(string $article): mixed
    {
        return $this->sendCommand(new Command\HeadCommand($article));
    }

    public function listGroups(?string $keyword = null, mixed $arguments = null): mixed
    {
        return $this->sendCommand(new Command\ListCommand($keyword, $arguments));
    }

    public function group(string $name): mixed
    {
        return $this->sendCommand(new Command\GroupCommand($name));
    }

    public function help(): mixed
    {
        return $this->sendCommand(new Command\HelpCommand());
    }

    public function overviewFormat(): mixed
    {
        return $this->sendCommand(new Command\OverviewFormatCommand());
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

    public function postArticle(string $groups, string $subject, string $body, string $from, ?string $headers = null): mixed
    {
        return $this->sendArticle(new Command\PostArticleCommand($groups, $subject, $body, $from, $headers));
    }

    public function quit(): mixed
    {
        return $this->sendCommand(new Command\QuitCommand());
    }

    public function xfeature(string $feature): mixed
    {
        return $this->sendCommand(new Command\XFeatureCommand($feature));
    }

    /**
     * @param array<string, bool> $format
     */
    public function xover(int $from, int $to, array $format): mixed
    {
        return $this->sendCommand(new Command\XoverCommand($from, $to, $format));
    }

    /**
     * @param array<string, bool> $format
     */
    public function xzver(int $from, int $to, array $format): mixed
    {
        return $this->sendCommand(new Command\XzverCommand($from, $to, $format));
    }

    public function sendCommand(CommandInterface $command): mixed
    {
        return $this->connection->sendCommand($command);
    }

    public function sendArticle(CommandInterface $command): mixed
    {
        return $this->connection->sendArticle($command);
    }
}
