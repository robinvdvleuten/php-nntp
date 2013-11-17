<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class QuitCommand extends Command implements CommandInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return 'QUIT';
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::CONNECTION_CLOSED => 'handleConnectionClosedResponse',
        );
    }

    public function getResult()
    {
        // This command doesn't have a result.
        return;
    }

    public function handleConnectionClosedResponse(ResponseInterface $response)
    {
        // We do nothing with the incoming response.
        return;
    }
}
