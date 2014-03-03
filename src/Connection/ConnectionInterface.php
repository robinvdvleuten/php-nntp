<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;

interface ConnectionInterface
{
    public function connect();

    public function disconnect();

    public function sendCommand(CommandInterface $command);
}
