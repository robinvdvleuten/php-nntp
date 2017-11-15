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
class XoverCommand extends OverviewCommand
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return sprintf('XOVER %d-%d', $this->from, $this->to);
    }
}
