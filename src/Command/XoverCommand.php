<?php

namespace Rvdv\Nntp\Command;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XoverCommand extends OverviewCommand
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('XOVER %d-%d', $this->from, $this->to);
    }
}
