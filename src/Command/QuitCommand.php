<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;

/**
 * QuitCommand.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class QuitCommand extends Command implements CommandInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return 'QUIT';
    }

    public function onConnectionClosing(Response $response)
    {
    }
}
