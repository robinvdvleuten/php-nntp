<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

abstract class Command implements CommandInterface
{
    /**
     * @var \Rvdv\Nntp\Response\ResponseInterface
     */
    private $response;

    /**
     * {@inheritDoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
