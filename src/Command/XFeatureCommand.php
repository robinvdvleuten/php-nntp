<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;

/**
 * XFeatureCommand.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XFeatureCommand extends Command implements CommandInterface
{
    const COMPRESS_GZIP = 'COMPRESS GZIP';

    /**
     * @var string
     */
    private $feature;

    /**
     * Constructor.
     *
     * @param string $feature The feature to enable
     */
    public function __construct($feature)
    {
        $this->feature = $feature;

        parent::__construct(false);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return sprintf('XFEATURE %s', $this->feature);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::XFEATURE_ENABLED => 'onXFeatureEnabled',
        );
    }

    public function onXFeatureEnabled(Response $response)
    {
        $this->result = true;
    }
}
