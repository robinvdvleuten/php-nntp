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
 * OverviewFormatCommand.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class OverviewFormatCommand extends Command implements CommandInterface
{
    public function __construct()
    {
        parent::__construct([], true);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return 'LIST OVERVIEW.FMT';
    }

    public function onInformationFollows(MultiLineResponse $response)
    {
        $result = [];

        foreach ($response->getLines() as $line) {
            if (0 == strcasecmp(substr($line, -5, 5), ':full')) {
                // ':full' is _not_ included in tag, but value set to true
                $result[str_replace('-', '_', strtolower(substr($line, 0, -5)))] = true;
            } else {
                // ':' is _not_ included in tag; value set to false
                $result[str_replace('-', '_', strtolower(substr($line, 0, -1)))] = false;
            }
        }

        return $result;
    }
}
