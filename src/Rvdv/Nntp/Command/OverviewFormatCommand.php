<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class OverviewFormatCommand extends Command implements CommandInterface
{
    /**
     * @var array
     */
    private $result = array();

    public function isMultiLine()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('LIST OVERVIEW.FMT');
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::OVERVIEW_FORMAT_FOLLOWS => 'handleOverviewFormatResponse',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    public function handleOverviewFormatResponse(ResponseInterface $response)
    {
        $this->result = array();

        foreach ($response->getLines() as $line) {
            if (0 == strcasecmp(substr($line, -5, 5), ':full')) {
                // ':full' is _not_ included in tag, but value set to true
                $this->result[str_replace('-', '_', strtolower(substr($line, 0, -5)))] = true;
            } else {
                // ':' is _not_ included in tag; value set to false
                $this->result[str_replace('-', '_', strtolower(substr($line, 0, -1)))] = false;
            }
        }
    }
}
