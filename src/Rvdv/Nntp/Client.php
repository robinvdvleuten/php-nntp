<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Connection\ConnectionInterface;

class Client implements ClientInterface
{
    /**
     * @var \Rvdv\Nntp\Connection\ConnectionInterface
     */
    private $connection;

    public static function create()
    {
        $client = new self();

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

    public function connect($host, $port, $secure = false, $timeout = 15)
    {
        return $this->connection->connect($host, $port, $secure, $timeout);
    }

    public function disconnect()
    {
        $response = $this->quit();

        if (!$this->connection->disconnect()) {
            // @todo throw exception
        }

        return $response;
    }

    /**
     * @method \Rvdv\Nntp\Response\ResponseInterface quit()
     */
    public function __call($command, $arguments)
    {
        $class = sprintf('Rvdv\Nntp\Command\%sCommand', str_replace(" ", "", ucwords(strtr($command, "_-", "  "))));
        if (!class_exists($class) || !in_array('Rvdv\Nntp\Command\CommandInterface', class_implements($class))) {
            throw new \RuntimeException(sprintf(
                "Given command string '%s' is mapped to a non-callable command class (%s).",
                $command,
                $class
            ));
        }

        $arguments = array_merge(array($this->connection), $arguments);

        $reflect  = new \ReflectionClass($class);
        $command = $reflect->newInstanceArgs($arguments);

        return $command->execute();
    }
}
