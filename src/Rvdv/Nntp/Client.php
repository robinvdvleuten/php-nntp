<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\AuthInfoCommand;
use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\ResponseInterface;

class Client implements ClientInterface
{
    /**
     * @var \Rvdv\Nntp\Connection\ConnectionInterface
     */
    private $connection;

    public static function create()
    {
        $client = new static();

        $connection = new Connection();
        $client->setConnection($connection);

        return $client;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function authenticate($username, $password)
    {
        $command = $this->authInfo(AuthInfoCommand::AUTHINFO_USER, $username);

        if (ResponseInterface::AUTHENTICATION_CONTINUE == $command->getResponse()->getStatusCode()) {
            $command = $this->authInfo(AuthInfoCommand::AUTHINFO_PASS, $password);
        }

        if (ResponseInterface::AUTHENTICATION_ACCEPTED != $command->getResponse()->getStatusCode()) {
            new \RuntimeException(sprintf(
                "Could not authenticate with the provided username/password: %s [%d]",
                $command->getResponse()->getMessage(),
                $command->getResponse()->getStatusCode()
            ));
        }

        return $command;
    }

    public function connect($host, $port, $secure = false, $timeout = 15)
    {
        return $this->connection->connect($host, $port, $secure, $timeout);
    }

    public function disconnect()
    {
        $command = $this->quit();

        if (!$this->connection->disconnect()) {
            // @todo throw exception
        }

        return $command;
    }

    /**
     * @method \Rvdv\Nntp\Command\CommandInterface authInfo($type, $value)
     * @method \Rvdv\Nntp\Command\CommandInterface group($name)
     * @method \Rvdv\Nntp\Command\CommandInterface overview($range, $format)
     * @method \Rvdv\Nntp\Command\CommandInterface overviewFormat()
     * @method \Rvdv\Nntp\Command\CommandInterface quit()
     */
    public function __call($command, $arguments)
    {
        $class = sprintf('Rvdv\Nntp\Command\%sCommand', str_replace(" ", "", ucwords(strtr($command, "_-", "  "))));
        if (!class_exists($class) || !in_array('Rvdv\Nntp\Command\CommandInterface', class_implements($class))) {
            throw new \InvalidArgumentException(sprintf(
                "Given command string '%s' is mapped to a non-callable command class (%s).",
                $command,
                $class
            ));
        }

        $reflect  = new \ReflectionClass($class);
        $command = $reflect->newInstanceArgs($arguments);

        return $this->connection->sendCommand($command);
    }
}
