<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\ResponseInterface;

class OverviewCommand extends Command implements CommandInterface
{
    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var array
     */
    private $format;

    /**
     * @var SplFixedArray
     */
    private $result;

    /**
     * Constructor
     *
     * @param int   $from   The article number where the range begins.
     * @param int   $to     The article number where the range ends.
     * @param array $format The format of the articles in response.
     */
    public function __construct($from, $to, array $format)
    {
        $this->from = $from;
        $this->to = $to;
        $this->format = array_merge(array('number' => false), $format);

        $size = ($this->to - $this->from) + 1;
        $this->result = new \SplFixedArray($size);
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
        return sprintf('XOVER %d-%d', $this->from, $this->to);
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
        $lines = $response->getLines();

        foreach ($lines as $index => $line) {
            $segments = explode("\t", $line);

            $field = 0;
            $article = new \stdClass();

            foreach ($this->format as $name => $full) {
                $value = $full ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                $article->{$name} = $value;

                $field++;
            }

            $this->result[$index] = $article;
        }

        unset($lines);
    }
}
