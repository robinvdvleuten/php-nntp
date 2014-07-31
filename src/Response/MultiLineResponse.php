<?php

namespace Rvdv\Nntp\Response;

/**
 * MultiLineResponse
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class MultiLineResponse implements MultiLineResponseInterface
{
    /**
     * @var \ArrayAccess
     */
    private $lines;

    /**
     * @var \Rvdv\Nntp\Response\ResponseInterface
     */
    private $response;

    /**
     * Constructor
     *
     * @param ResponseInterface $response A ResponseInterface instance.
     * @param \ArrayAccess      $lines    An ArrayAccess instance.
     */
    public function __construct(ResponseInterface $response, \ArrayAccess $lines)
    {
        $this->response = $response;
        $this->lines = $lines;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return $this->response->getMessage();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritDoc}
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->response;
    }
}
