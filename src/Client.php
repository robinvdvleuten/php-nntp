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

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class Client implements ClientInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        return $this->connection->connect();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        $this->connection->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password = null)
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

    /**
     * {@inheritdoc}
     */
    public function connectAndAuthenticate($username = null, $password = null)
    {
        $response = $this->connect();

        if (!in_array($response->getStatusCode(), [Response::$codes['PostingAllowed'], Response::$code['PostingProhibited']])) {
            throw new RuntimeException(sprintf('Unsuccessful response received: %s', (string) $response));
        }

        if ($username === null) {
            return $response;
        }

        return $this->authenticate($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function authInfo($type, $value)
    {
        return $this->sendCommand(new Command\AuthInfoCommand($type, $value));
    }

    /**
     * {@inheritdoc}
     */
    public function body($article)
    {
        return $this->sendCommand(new Command\BodyCommand($article));
    }

    /**
     * {@inheritdoc}
     */
    public function listGroups($keyword = null, $arguments = null)
    {
        return $this->sendCommand(new Command\ListCommand($keyword, $arguments));
    }

    /**
     * {@inheritdoc}
     */
    public function group($name)
    {
        return $this->sendCommand(new Command\GroupCommand($name));
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return $this->sendCommand(new Command\HelpCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function overviewFormat()
    {
        return $this->sendCommand(new Command\OverviewFormatCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function post($groups, $subject, $body, $from, $headers = null)
    {
        $command = $this->sendCommand(new Command\PostCommand());
        $response = $command->getResponse();

        if ($response->getStatusCode() === Response::$codes['SendArticle']) {
            $response = $this->postArticle($groups, $subject, $body, $from, $headers);
        }

        if ($response->getStatusCode() !== Response::$codes['ArticleReceived']) {
            throw new RuntimeException(sprintf('Posting failed: %s', (string) $response));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function postArticle($groups, $subject, $body, $from, $headers = null)
    {
        return $this->sendArticle(new Command\PostArticleCommand($groups, $subject, $body, $from, $headers));
    }

    /**
     * {@inheritdoc}
     */
    public function quit()
    {
        return $this->sendCommand(new Command\QuitCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function xfeature($feature)
    {
        return $this->sendCommand(new Command\XFeatureCommand($feature));
    }

    /**
     * {@inheritdoc}
     */
    public function xover($from, $to, array $format)
    {
        return $this->sendCommand(new Command\XoverCommand($from, $to, $format));
    }

    /**
     * {@inheritdoc}
     */
    public function xzver($from, $to, array $format)
    {
        return $this->sendCommand(new Command\XzverCommand($from, $to, $format));
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        return $this->connection->sendCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function sendArticle(CommandInterface $command)
    {
        return $this->connection->sendArticle($command);
    }
}
