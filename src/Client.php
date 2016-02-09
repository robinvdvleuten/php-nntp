<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
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
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Client implements ClientInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * Constructor
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
        if (!$this->connection->disconnect()) {
            throw new RuntimeException('Error while disconnecting from NNTP server');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password = null)
    {
        $command = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_USER, (string) $username);
        $response = $command->getResponse();

        if ($response->getStatusCode() === Response::PASSWORD_REQUIRED) {
            if (null === $password) {
                throw new RuntimeException('NNTP server asks for further authentication but no password is given');
            }

            $command = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_PASS, (string) $password);
            $response = $command->getResponse();
        }

        if ($response->getStatusCode() !== Response::AUTHENTICATION_ACCEPTED) {
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

        if (!in_array($response->getStatusCode(), array(Response::POSTING_ALLOWED, RESPONSE::POSTING_PROHIBITED))) {
            throw new RuntimeException(sprintf('Unsuccessful response received: %s', (string) $response));
        }

        if ($username !== null) {
            return $this->authenticate($username, $password);
        }

        return $response;
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

        if ($response->getStatusCode() === Response::SEND_ARTICLE) {
            $command = $this->postArticle($groups, $subject, $body, $from, $headers);
            $response = $command->getResponse();
        }

        if ($response->getStatusCode() !== Response::ARTICLE_RECEIVED) {
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
