<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;

/**
 * XFeatureCommand
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
     * Constructor
     *
     * @param string $feature The feature to enable
     */
    public function __construct($feature)
    {
        $this->feature = $feature;

        parent::__construct(false);
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('XFEATURE %s', $this->feature);
    }

    /**
     * {@inheritDoc}
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
