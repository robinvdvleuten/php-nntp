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
    /**
     * @var string
     */
    private $groups;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $headers;

   /**
    * Constructor.
    */
   public function __construct($groups, $subject, $body, $from, $headers)
   {
       $this->groups = $groups;
       $this->subject = $subject;
       $this->body = $body;
       $this->from = $from;
       $this->headers = $headers;

       parent::__construct();
   }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $article = [
            'From: '.$this->from,
            'Newsgroups: '.$this->groups,
            'Subject: '.$this->subject,
            'X-poster: php-nntp',
        ];

        if ($this->headers !== null) {
            $article[] = $this->headers;
        }

        $article[] = "\r\n".$this->body;

        return implode("\r\n", $article);
    }

    public function onArticleReceived(Response $response)
    {
        return $response;
    }

    public function onPostingFailed(Response $response)
    {
        throw new RuntimeException(sprintf('Posting failed: %s', $response->getMessage()), $response->getStatusCode());
    }
}
