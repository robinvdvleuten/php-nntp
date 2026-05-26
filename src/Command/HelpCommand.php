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

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class HelpCommand extends Command implements CommandInterface
{
    public function __construct()
    {
        parent::__construct(true);
    }

    public function __invoke(): string
    {
        return 'HELP';
    }

    /**
     * @return string
     */
    public function onHelpTextFollows(MultiLineResponse $response): string
    {
        return implode("\n", (array) $response->getLines());
    }
}
