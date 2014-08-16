<?php

namespace Rvdv\Nntp\Response;

interface ResponseInterface
{
    public function getMessage();

    public function getStatusCode();

    /**
     * Get a string representation of the response
     * @return string
     */
    public function __toString();
}
