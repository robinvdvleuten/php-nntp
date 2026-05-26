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
use Rvdv\Nntp\Response\Response;

/**
 * GroupCommand.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class GroupCommand extends Command implements CommandInterface
{
    private string $group;

    /**
     * @param string $group the name of the group
     */
    public function __construct(string $group)
    {
        $this->group = $group;

        parent::__construct();
    }

    public function __invoke(): string
    {
        return sprintf('GROUP %s', $this->group);
    }

    /**
     * @return array{count: string, first: string, last: string, name: string}
     */
    public function onGroupSelected(Response $response): array
    {
        $segments = explode(' ', $response->getMessage(), 4);
        if (4 !== count($segments)) {
            throw new RuntimeException(sprintf('Invalid group selected response: %s', $response->getMessage()));
        }

        return [
            'count' => $segments[0],
            'first' => $segments[1],
            'last' => $segments[2],
            'name' => $segments[3],
        ];
    }

    public function onNoSuchGroup(Response $response): void
    {
        throw new RuntimeException(sprintf('A group with name %s does not exists on server', $this->group));
    }
}
