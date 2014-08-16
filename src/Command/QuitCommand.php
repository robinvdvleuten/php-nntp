<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;

/**
 * QuitCommand
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
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

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::CONNECTION_CLOSING => 'onConnectionClosing',
        );
    }

    public function onConnectionClosing(Response $response)
    {
        return;
    }
}
