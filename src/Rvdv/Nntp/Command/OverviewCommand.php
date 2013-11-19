<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class OverviewCommand extends Command implements CommandInterface
{
    /**
     * @var array
     */
    private $format;

    /**
     * @var string
     */
    private $range;

    /**
     * @var array
     */
    private $result = array();

    /**
     * Constructor
     *
     * @param string $range  The range of the overview.
     * @param array  $format The format of the articles in response.
     */
    public function __construct($range, array $format)
    {
        $this->range = $range;
        $this->format = array_merge(array('number' => false), $format);
    }

    public function isMultiLine()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('XOVER %s', $this->range);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::OVERVIEW_FOLLOWS => 'handleOverviewResponse',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    public function handleOverviewResponse(ResponseInterface $response)
    {
        $this->result = array();

        foreach ($response->getLines() as $line) {
            $segments = explode("\t", $line);

            $field = 0;
            $article = array();

            foreach ($this->format as $name => $full) {
                $value = $full ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                $article[$name] = $value;

                $field++;
            }

            $this->result[] = $article;
        }
    }
}
