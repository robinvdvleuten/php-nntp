<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Command;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
abstract class Command implements CommandInterface
{
    protected bool $isMultiLine;

    public function __construct(bool $isMultiLine = false)
    {
        $this->isMultiLine = $isMultiLine;
    }

    public function isMultiLine(): bool
    {
        return $this->isMultiLine;
    }

    public function isCompressed(): bool
    {
        return false;
    }
}
