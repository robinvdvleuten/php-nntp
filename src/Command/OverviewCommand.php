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
    protected int $from;

    protected int $to;

    /**
     * @var array<string, bool>
     */
    protected array $format;

    /**
     * @param int   $from   the article number where the range begins
     * @param int   $to     the article number where the range ends
     * @param array<string, bool> $format the format of the articles in response
     */
    public function __construct(int $from, int $to, array $format)
    {
        $this->from = $from;
        $this->to = $to;
        $this->format = array_merge(['number' => false], $format);

        parent::__construct(true);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function onOverviewInformationFollows(MultiLineResponse $response): array
    {
        return array_map(function (string $line): array {
            $segments = explode("\t", $line);
            $field = 0;

            return array_reduce(array_keys($this->format), function (array $message, string $name) use ($segments, &$field): array {
                $message[$name] = $this->format[$name] ? ltrim(substr($segments[$field], strpos($segments[$field], ':') + 1), " \t") : $segments[$field];
                ++$field;

                return $message;
            }, []);
        }, $response->getLines());
    }

    public function onNoNewsGroupCurrentSelected(Response $response): never
    {
        throw new RuntimeException('A group must be selected first before getting an overview.');
    }

    public function onNoArticlesSelected(Response $response): never
    {
        throw new RuntimeException(sprintf('No articles selected in the given range %d-%d.', $this->from, $this->to));
    }
}
