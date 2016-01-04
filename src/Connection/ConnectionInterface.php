<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @return bool
     */
    public function disconnect();

    /**
     * @return CommandInterface
     */
    public function sendCommand(CommandInterface $command);
}
