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

class ListCommand extends Command
{
    const KEYWORD_ACTIVE = 'ACTIVE';
    const KEYWORD_ACTIVE_TIMES = 'ACTIVE.TIMES';
    const KEYWORD_DISTRIB_PATS = 'DISTRIB.PATS';
    const KEYWORD_HEADERS = 'HEADERS';
    const KEYWORD_NEWSGROUPS = 'NEWSGROUPS';
    const KEYWORD_OVERVIEW_FMT = 'OVERVIEW.FMT';

    /**
     * @var string
     */
    protected $keyword;

    /**
     * @var string
     */
    protected $arguments;

    /**
     * @param string $keyword
     * @param string $arguments
     */
    public function __construct($keyword = null, $arguments = null)
    {
        $this->keyword = $keyword;
        $this->arguments = $arguments;
        parent::__construct([], true);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return trim(sprintf('LIST %s %s', $this->keyword, $this->arguments));
    }

    /**
     * @param MultiLineResponse $response
     *
     * @return array
     */
    public function onListFollows(MultiLineResponse $response)
    {
        $lines = $response->getLines();
        $totalLines = count($lines);

        $result = [];

        for ($i = 0; $i < $totalLines; ++$i) {
            list($name, $high, $low, $status) = explode(' ', $lines[$i]);

            $result[$i] = [
                'name' => $name,
                'high' => $high,
                'low' => $low,
                'status' => $status,
            ];
        }

        return $result;
    }

    public function onInvalidKeyword(Response $response)
    {
        throw new RuntimeException('Invalid keyword, or unexpected argument for keyword.', $response->getStatusCode());
    }

    public function onError(Response $response)
    {
        throw new RuntimeException('Error retrieving group list', $response->getStatusCode());
    }
}
