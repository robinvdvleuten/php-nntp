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
    /**
     * @return boolean
     */
    public function isMultiLine();

    /**
     * @return boolean
     */
    public function isCompressed();

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @return void
     */
    public function setResponse(ResponseInterface $response);

    public function getExpectedResponseCodes();

    public function getResult();

    /**
     * @return string
     */
    public function execute();
}
