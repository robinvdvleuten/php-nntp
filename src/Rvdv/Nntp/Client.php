<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Connection\ConnectionInterface;

class Client
{
    /**
     * @var Rvdv\Nntp\Connection\ConnectionInterface
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
}
