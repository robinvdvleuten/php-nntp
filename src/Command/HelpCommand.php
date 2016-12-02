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

use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class HelpCommand extends Command implements CommandInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct([], true);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return 'HELP';
    }

    /**
     * Called when help text is received from server.
     *
     * @param MultiLineResponse $response
     */
    public function onHelpTextFollow(MultiLineResponse $response)
    {
        $this->result = implode("\n", (array) $response->getLines());
    }
}
