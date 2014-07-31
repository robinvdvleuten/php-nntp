<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XzverCommand extends XoverCommand implements CommandInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return sprintf('XZVER %d-%d', $this->from, $this->to);
    }

    /**
     * {@inheritdoc}
     */
    public function isCompressed()
    {
        return true;
    }
}
