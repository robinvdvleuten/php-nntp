<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
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

    public function disconnect();

    /**
     * @param CommandInterface $command
     *
     * @return mixed
     */
    public function sendCommand(CommandInterface $command);

    /**
     * @param CommandInterface $command
     *
     * @return mixed
     */
    public function sendArticle(CommandInterface $command);
}
