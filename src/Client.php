<?php

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
        $command = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_USER, $username);
        $response = $command->getResponse();

        if ($response->getStatusCode() === Response::PASSWORD_REQUIRED) {
            if (null === $password) {
                throw new RuntimeException('NNTP server asks for further authentication but no password is given');
            }

            $command = $this->authInfo(Command\AuthInfoCommand::AUTHINFO_PASS, $password);
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
    public function xoverview($from, $to, array $format)
    {
        return $this->sendCommand(new Command\XOverviewCommand($from, $to, $format));
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        return $this->connection->sendCommand($command);
    }
}
