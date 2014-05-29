<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * Client
 *
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
     * {@inheritDoc}
     */
    public function connect()
    {
        return $this->connection->connect();
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        if (!$this->connection->disconnect()) {
            throw new RuntimeException('Error while disconnecting from NNTP server');
        }

        return true;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function authInfo($type, $value)
    {
        return $this->sendCommand(new Command\AuthInfoCommand($type, $value));
    }

    /**
     * {@inheritDoc}
     */
    public function group($name)
    {
        return $this->sendCommand(new Command\GroupCommand($name));
    }

    /**
     * {@inheritDoc}
     */
    public function overview($from, $to, array $format)
    {
        return $this->sendCommand(new Command\OverviewCommand($from, $to, $format));
    }

    /**
     * {@inheritDoc}
     */
    public function overviewFormat()
    {
        return $this->sendCommand(new Command\OverviewFormatCommand());
    }

    /**
     * {@inheritDoc}
     */
    public function quit()
    {
        return $this->sendCommand(new Command\QuitCommand());
    }

    /**
     * {@inheritDoc}
     */
    public function xfeature($feature)
    {
        return $this->sendCommand(new Command\XFeatureCommand($feature));
    }

    /**
     * {@inheritDoc}
     */
    public function sendCommand(CommandInterface $command)
    {
        return $this->connection->sendCommand($command);
    }
}
