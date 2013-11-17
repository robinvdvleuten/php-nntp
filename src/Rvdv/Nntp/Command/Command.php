<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;

abstract class Command implements CommandInterface
{
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function execute()
    {
        return $this->connection->sendCommand($this->__toString());
    }
}
