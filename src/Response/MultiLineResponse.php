<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Response;

use Rvdv\Nntp\Response\ResponseInterface;

/**
 * MultiLineResponse.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class MultiLineResponse implements MultiLineResponseInterface
{
    /**
     * @var array
     */
    private $lines;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Constructor.
     *
     * @param ResponseInterface $response
     * @param array             $lines
     */
    public function __construct(ResponseInterface $response, array $lines)
    {
        $this->response = $response;
        $this->lines = $lines;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->response->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->response;
    }
}
