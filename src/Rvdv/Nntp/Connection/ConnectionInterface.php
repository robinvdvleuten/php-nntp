<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;

interface ConnectionInterface
{
    function connect($host, $port, $secure = false, $timeout = 15);

    function disconnect();

    function sendCommand(CommandInterface $command);
}
