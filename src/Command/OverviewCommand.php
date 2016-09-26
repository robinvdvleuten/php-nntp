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

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
abstract class OverviewCommand extends Command implements CommandInterface
{
    /**
     * @var int
     */
    protected $from;

    /**
     * @var int
     */
    protected $to;

    /**
     * @var array
     */
    protected $format;

    /**
     * Constructor.
     *
     * @param int   $from   The article number where the range begins.
     * @param int   $to     The article number where the range ends.
     * @param array $format The format of the articles in response.
     */
    public function __construct($from, $to, array $format)
    {
        $this->from = $from;
        $this->to = $to;
        $this->format = array_merge(['number' => false], $format);

        parent::__construct(new \SplFixedArray($this->to - $this->from + 1), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return [
            Response::OVERVIEW_INFORMATION_FOLLOWS  => 'onOverviewInformationFollows',
            Response::NO_NEWSGROUP_CURRENT_SELECTED => 'onNoNewsGroupCurrentSelected',
            Response::NO_ARTICLES_SELECTED          => 'onNoArticlesSelected',
        ];
    }

    public function onOverviewInformationFollows(MultiLineResponse $response)
    {
        $lines = $response->getLines();
        $totalLines = count($lines);

        for ($i = 0; $i < $totalLines; ++$i) {
            $segments = explode("\t", $lines[$i]);

            $field = 0;
            $message = [];

            foreach ($this->format as $name => $full) {
                $value = $full ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                $message[$name] = $value;

                ++$field;
            }

            $this->result[$i] = $message;
        }

        unset($lines);
    }

    public function onNoNewsGroupCurrentSelected(Response $response)
    {
        throw new RuntimeException('A group must be selected first before getting an overview.');
    }

    public function onNoArticlesSelected(Response $response)
    {
        throw new RuntimeException(sprintf('No articles selected in the given range %d-%d.', $this->from, $this->to));
    }
}
