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
 * PostCommand.
 *
 * @author thebandit
 */
class PostArticleCommand extends Command implements CommandInterface
{
    private string $groups;

    private string $subject;

    private string $body;

    private string $from;

    private ?string $headers;

    public function __construct(string $groups, string $subject, string $body, string $from, ?string $headers)
    {
        $this->groups = $groups;
        $this->subject = $subject;
        $this->body = $body;
        $this->from = $from;
        $this->headers = $headers;

        parent::__construct();
    }

    public function __invoke(): string
    {
        return $this->dotStuffLines($this->buildArticle());
    }

    private function buildArticle(): string
    {
        $article = [
            'From: '.$this->from,
            'Newsgroups: '.$this->groups,
            'Subject: '.$this->subject,
            'X-poster: php-nntp',
        ];

        if (null !== $this->headers) {
            $article[] = $this->headers;
        }

        $article[] = "\r\n".$this->body;

        return $this->normalizeLineEndings(implode("\r\n", $article));
    }

    private function normalizeLineEndings(string $value): string
    {
        $value = str_replace("\r\n", "\n", $value);
        $value = str_replace("\r", "\n", $value);

        return str_replace("\n", "\r\n", $value);
    }

    private function dotStuffLines(string $article): string
    {
        return implode("\r\n", array_map(
            static fn (string $line): string => str_starts_with($line, '.') ? '.'.$line : $line,
            explode("\r\n", $article)
        ));
    }

    public function onArticleReceived(Response $response): Response
    {
        return $response;
    }

    public function onPostingFailed(Response $response): void
    {
        throw new RuntimeException(sprintf('Posting failed: %s', $response->getMessage()), (int) $response->getStatusCode());
    }
}
