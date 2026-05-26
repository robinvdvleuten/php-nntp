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
 * BodyCommand.
 *
 * @author thebandit
 */
class BodyCommand extends Command implements CommandInterface
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
        return sprintf('BODY %s', $this->article);
    }

    public function onBodyFollows(MultiLineResponse $response): string
    {
        return implode("\r\n", $response->getLines());
    }

    public function onNoNewsGroupCurrentSelected(Response $response): never
    {
        throw new RuntimeException('A group must be selected first before getting an article body.', (int) $response->getStatusCode());
    }

    public function onNoSuchArticleNumber(Response $response): never
    {
        throw new RuntimeException('No article with that number.', (int) $response->getStatusCode());
    }

    public function onNoSuchArticleId(Response $response): never
    {
        throw new RuntimeException('No article with that message-id.', (int) $response->getStatusCode());
    }
}
