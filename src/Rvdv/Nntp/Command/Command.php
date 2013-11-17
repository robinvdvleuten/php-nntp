<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\ResponseInterface;

abstract class Command implements CommandInterface
{
    /**
     * @var \Rvdv\Nntp\Connection\ConnectionInterface
     */
    private $connection;

    /**
     * @var \Rvdv\Nntp\Response\ResponseInterface
     */
    private $response;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

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
