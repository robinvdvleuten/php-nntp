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

use Rvdv\Nntp\Response\Response;

/**
 * XFeatureCommand.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class XFeatureCommand extends Command implements CommandInterface
{
    public const COMPRESS_GZIP = 'COMPRESS GZIP';

    private string $feature;

    /**
     * @param string $feature The feature to enable
     */
    public function __construct(string $feature)
    {
        $this->feature = $feature;

        parent::__construct();
    }

    public function __invoke(): string
    {
        return sprintf('XFEATURE %s', $this->feature);
    }

    public function onXFeatureEnabled(Response $response): bool
    {
        return true;
    }
}
