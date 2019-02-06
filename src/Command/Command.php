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
    /**
     * @var bool
     */
    protected $isMultiLine;

    /**
     * Constructor.
     *
     * @param bool $isMultiLine a bool indicating the response is multiline or not
     */
    public function __construct($isMultiLine = false)
    {
        $this->isMultiLine = $isMultiLine;
    }

    /**
     * {@inheritdoc}
     */
    public function isMultiLine()
    {
        return $this->isMultiLine;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompressed()
    {
        return false;
    }
}
