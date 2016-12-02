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
     * @param int   $from   the article number where the range begins
     * @param int   $to     the article number where the range ends
     * @param array $format the format of the articles in response
     */
    public function __construct($from, $to, array $format)
    {
        $this->from = $from;
        $this->to = $to;
        $this->format = array_merge(['number' => false], $format);

        parent::__construct(new \SplFixedArray($this->to - $this->from + 1), true);
    }

    public function onOverviewInformationFollows(MultiLineResponse $response)
    {
        return array_map(function ($line) {
            $segments = explode("\t", $line);
            $field = 0;

            return array_reduce(array_keys($this->format), function ($message, $name) use ($segments, &$field) {
                $message[$name] = $this->format[$name] ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                ++$field;

                return $message;
            }, []);
        }, $response->getLines());
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
