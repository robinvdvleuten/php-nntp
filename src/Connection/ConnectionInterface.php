<?php

namespace Rvdv\Nntp\Connection;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Response\Response;

interface ConnectionInterface
{
    /**
     * @return Response
     */
    public function connect();

    /**
     * @return boolean
     */
    public function disconnect();

    /**
     * @return CommandInterface
     */
    public function sendCommand(CommandInterface $command);
}
