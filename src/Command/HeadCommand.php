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
    /**
     * @var string
     */
    private $article;

    /**
     * Constructor.
     *
     * @param string $article
     */
    public function __construct($article)
    {
        $this->article = $article;

        parent::__construct(true);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return sprintf('HEAD %s', $this->article);
    }

    /**
     * @return string
     */
    public function onHeadFollows(MultiLineResponse $response)
    {
        return array_reduce($response->getLines(), function ($headers, $line) {
            preg_match('/^([^\:]+)\:\s*(.*)$/', $line, $matches);
            if (!empty($matches)) {
                $headers[$matches[1]] = trim($matches[2]);
            }

            return $headers;
        });
    }

    public function onNoNewsGroupCurrentSelected(Response $response)
    {
        throw new RuntimeException('A group must be selected first before getting an article header.', $response->getStatusCode());
    }

    public function onNoSuchArticleNumber(Response $response)
    {
        throw new RuntimeException('No article with that number.', $response->getStatusCode());
    }

    public function onNoSuchArticleId(Response $response)
    {
        throw new RuntimeException('No article with that message-id.', $response->getStatusCode());
    }
}
