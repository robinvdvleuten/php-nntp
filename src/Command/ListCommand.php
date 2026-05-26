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
    public const KEYWORD_ACTIVE = 'ACTIVE';
    public const KEYWORD_ACTIVE_TIMES = 'ACTIVE.TIMES';
    public const KEYWORD_DISTRIB_PATS = 'DISTRIB.PATS';
    public const KEYWORD_HEADERS = 'HEADERS';
    public const KEYWORD_NEWSGROUPS = 'NEWSGROUPS';
    public const KEYWORD_OVERVIEW_FMT = 'OVERVIEW.FMT';

    protected ?string $keyword;

    protected mixed $arguments;

    public function __construct(?string $keyword = null, mixed $arguments = null)
    {
        $this->keyword = $keyword;
        $this->arguments = $arguments;

        parent::__construct(true);
    }

    public function __invoke(): string
    {
        return trim(sprintf('LIST %s %s', $this->keyword, $this->arguments));
    }

    /**
     * @return array<int, array{name: string, high: string, low: string, status: string}>
     */
    public function onInformationFollows(MultiLineResponse $response): array
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

    public function onInvalidKeyword(Response $response): void
    {
        throw new RuntimeException('Invalid keyword, or unexpected argument for keyword.', (int) $response->getStatusCode());
    }

    public function onError(Response $response): void
    {
        throw new RuntimeException('Error retrieving group list', (int) $response->getStatusCode());
    }
}
