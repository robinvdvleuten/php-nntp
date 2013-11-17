<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class AuthInfoCommand extends Command implements CommandInterface
{
    const AUTHINFO_USER = 'USER';
    const AUTHINFO_PASS = 'PASS';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function isMultiLine()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('AUTHINFO %s %s', $this->type, $this->value);
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

    public function getResult()
    {
        // This command doesn't have a result.
        return;
    }

    public function handleAuthenticatedResponse(ResponseInterface $response)
    {
        // We do nothing with the incoming response.
        return;
    }
}
