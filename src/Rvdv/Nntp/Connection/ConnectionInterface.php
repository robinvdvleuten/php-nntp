<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;

interface ConnectionInterface
{
    function connect();

    function disconnect();

    function sendCommand(CommandInterface $command);
}
