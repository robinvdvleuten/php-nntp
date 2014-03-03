<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

/**
 * CommandInterface
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
interface CommandInterface
{
    function isMultiLine();

    function getResponse();

    function setResponse(ResponseInterface $response);

    function getExpectedResponseCodes();

    function getResult();

    function execute();
}
