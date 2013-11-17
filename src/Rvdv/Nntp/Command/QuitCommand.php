<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;

class QuitCommand implements CommandInterface
{
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function execute()
    {
        return $this->connection->sendCommand('QUIT');
    }
}
