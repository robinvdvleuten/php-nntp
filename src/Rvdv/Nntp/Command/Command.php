<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * Command
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class Command implements CommandInterface
{
    /**
     * @var bool
     */
    protected $isMultiline;

    /**
     * @var \Rvdv\Nntp\Response\Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * Constructor
     *
     * @param mixed $result      The default result for this command.
     * @param bool  $isMultiline A bool indicating the response is multiline or not.
     */
    public function __construct($result = null, $isMultiline = false)
    {
        $this->isMultiLine = $isMultiline;
        $this->result = $result;
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

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function isMultiLine()
    {
        return $this->isMultiLine;
    }
}
