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
use Rvdv\Nntp\Response\ResponseInterface;

interface ConnectionInterface
{
    public function connect(): ResponseInterface;

    public function disconnect(): void;

    public function sendCommand(CommandInterface $command): mixed;

    public function sendArticle(CommandInterface $command): mixed;
}
