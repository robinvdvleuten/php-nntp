<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
