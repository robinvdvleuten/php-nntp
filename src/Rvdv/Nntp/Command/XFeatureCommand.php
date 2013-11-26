<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class XFeatureCommand extends Command implements CommandInterface
{
    const XFEATURE_COMPRESS_GZIP = 'COMPRESS GZIP';

    /**
     * @var string
     */
    private $feature;

    /**
     * @var array
     */
    private $result = false;

    /**
     * Constructor
     *
     * @param string $feature The feature to enable
     */
    public function __construct($feature)
    {
        $this->feature = $feature;
    }

    public function isMultiLine()
    {
        return false;
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
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::FEATURE_ENABLED => 'handleXFeatureResponse',
            ResponseInterface::UNKNOWN_COMMAND => 'handleXFeatureResponse',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    public function handleXFeatureResponse(ResponseInterface $response)
    {
        $this->result = $response->getStatusCode() === ResponseInterface::FEATURE_ENABLED;
    }
}
