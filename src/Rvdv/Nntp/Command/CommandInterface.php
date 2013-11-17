<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

interface CommandInterface
{
    function getResponse();

    function setResponse(ResponseInterface $response);

    function getResponseHandlers();

    function execute();
}
