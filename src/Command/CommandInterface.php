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
    public function isMultiLine();

    public function getResponse();

    public function setResponse(ResponseInterface $response);

    public function getExpectedResponseCodes();

    public function getResult();

    public function execute();
}
