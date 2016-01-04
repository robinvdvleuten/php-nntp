<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

/**
 * CommandInterface.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
interface CommandInterface
{
    /**
     * @return bool
     */
    public function isMultiLine();

    /**
     * @return bool
     */
    public function isCompressed();

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response);

    public function getExpectedResponseCodes();

    public function getResult();

    /**
     * @return string
     */
    public function execute();
}
