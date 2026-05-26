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
 * HeadCommand.
 *
 * @author elabz
 */
class HeadCommand extends Command implements CommandInterface
{
    private string $article;

    /**
     * @param string $article
     */
    public function __construct(string $article)
    {
        $this->article = $article;

        parent::__construct(true);
    }

    public function __invoke(): string
    {
        return sprintf('HEAD %s', $this->article);
    }

    public function onHeadFollows(MultiLineResponse $response): string
    {
        return implode("\r\n", $response->getLines());
    }

    public function onNoNewsGroupCurrentSelected(Response $response): void
    {
        throw new RuntimeException('A group must be selected first before getting an article header.', (int) $response->getStatusCode());
    }

    public function onNoSuchArticleNumber(Response $response): void
    {
        throw new RuntimeException('No article with that number.', (int) $response->getStatusCode());
    }

    public function onNoSuchArticleId(Response $response): void
    {
        throw new RuntimeException('No article with that message-id.', (int) $response->getStatusCode());
    }
}
