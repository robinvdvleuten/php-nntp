<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * PostCommand
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
        
        parent::__construct(array());
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $article  = "From: ".$this->from."\r\n";
        $article .= "Newsgroups: ".$this->groups."\r\n";
        $article .= "Subject: ".$this->subject."\r\n";
        $article .= "X-poster: php-nntp\r\n";
    	if ($this->headers !== null) {
    	    $article .= $this->headers."\r\n";
    	}
        $article .= "\r\n";
		$article .= $this->body;
		
        return $article;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::ARTICLE_RECEIVED => 'onArticleReceived',
            Response::POSTING_FAILED => 'onPostingFailed',
        );
    }
    
    public function onArticleReceived(Response $response)
    {
    	return true;
    }

    public function onPostingFailed(Response $response)
    {
        throw new RuntimeException(Response::POSTING_FAILED.' Posting failed: '.$response->getMessage());
    }
}
