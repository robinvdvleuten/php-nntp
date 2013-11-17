<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\ResponseInterface;

class AuthInfoCommand extends Command implements CommandInterface
{
    const AUTHINFO_USER = 'USER';
    const AUTHINFO_PASS = 'PASS';

    public function __construct(ConnectionInterface $connection, $type, $value)
    {
        $this->type = $type;
        $this->value = $value;

        parent::__construct($connection);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::AUTHENTICATION_ACCEPTED => 'handleAuthenticatedResponse',
            ResponseInterface::AUTHENTICATION_CONTINUE => 'handleAuthenticatedResponse',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('AUTHINFO %s %s', $this->type, $this->value);
    }

    public function handleAuthenticatedResponse(ResponseInterface $response)
    {
        return true;
    }
}
