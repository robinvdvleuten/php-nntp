<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command;
use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Exception\RuntimeException;

/**
 * Client
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Client implements ClientInterface
{
    /**
     * @var \Rvdv\Nntp\Connection\ConnectionInterface
     */
    private $connection;

    /**
     * Constructor
     *
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection A ConnectionInterface instance.
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the connection instance.
     *
     * @return \Rvdv\Nntp\Connection\ConnectionInterface
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
