<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class QuitCommand extends Command implements CommandInterface
{
    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::CONNECTION_CLOSED => 'handleConnectionClosedResponse',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return 'QUIT';
    }

    public function handleConnectionClosedResponse(ResponseInterface $response)
    {
        return true;
    }
}
