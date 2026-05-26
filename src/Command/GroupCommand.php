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
     * @return array{count: string, first: string, last: string, name: string}|false
     */
    public function onGroupSelected(Response $response): array|false
    {
        return array_combine(['count', 'first', 'last', 'name'], explode(' ', $response->getMessage()));
    }

    public function onNoSuchGroup(Response $response): void
    {
        throw new RuntimeException(sprintf('A group with name %s does not exists on server', $this->group));
    }
}
