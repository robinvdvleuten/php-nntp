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
class PostCommand extends Command implements CommandInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke(): string
    {
        return 'POST';
    }

    public function onSendArticle(Response $response): Response
    {
        return $response;
    }

    public function onPostingNotPermitted(Response $response): void
    {
        throw new RuntimeException('Posting not permitted.', (int) $response->getStatusCode());
    }
}
