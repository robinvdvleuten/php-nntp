<?php

namespace Rvdv\Nntp\Response;

class MultiLineResponse implements MultiLineResponseInterface
{
    /**
     * @var array
     */
    private $lines;

    /**
     * @var \Rvdv\Nntp\Response\ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response, \ArrayAccess $lines)
    {
        $this->response = $response;
        $this->lines = $lines;
    }

    public function getMessage()
    {
        return $this->response->getMessage();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function getLines()
    {
        return $this->lines;
    }
}
