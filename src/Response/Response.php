<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Response;

use Rvdv\Nntp\Exception\InvalidArgumentException;
use Rvdv\Nntp\Exception\RuntimeException;

/**
 * Response.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Response implements ResponseInterface
{
    const HELP_TEXT_FOLLOWS = 100; // RFC 3977

    const POSTING_ALLOWED = 200; // RFC 3977
    const POSTING_PROHIBITED = 201; // RFC 3977

    const CONNECTION_CLOSING = 205; // RFC 3977
    const GROUP_SELECTED = 211; // RFC 3977
    const INFORMATION_FOLLOWS = 215; // RFC 2980
    const BODY_FOLLOWS = 222; //RFC 3977
    const OVERVIEW_INFORMATION_FOLLOWS = 224; // RFC 2980
    const ARTICLE_RECEIVED = 240; //RFC 3977
    const AUTHENTICATION_ACCEPTED = 281; // RFC 4643
    const XFEATURE_ENABLED = 290;

    const SEND_ARTICLE = 340; // RFC 3977
    const PASSWORD_REQUIRED = 381; // RFC 4643

    const NO_SUCH_GROUP = 411; // RFC 3977
    const NO_NEWSGROUP_CURRENT_SELECTED = 412; // RFC 2980
    const NO_ARTICLES_SELECTED = 420; // RFC 2980
    const NO_SUCH_ARTICLE_NUMBER = 423; // RFC 3977
    const NO_SUCH_ARTICLE_ID = 430; // RFC 3977
    const POSTING_NOT_PERMITTED = 440; // RFC 3977
    const POSTING_FAILED = 441; // RFC 3977
    const AUTHENTICATION_REJECTED = 481; // RFC 4643
    const AUTHENTICATION_OUTOFSEQUENCE = 482; // RFC 4643

    const COMMAND_UNKNOWN = 500; // RFC 3977
    const COMMAND_UNAVAILABLE = 502; // RFC 4643
    const PROGRAM_ERROR = 503; // RFC 2980

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param string $response
     */
    public static function createFromString($response)
    {
        if (false === strpos($response, "\r\n")) {
            throw new InvalidArgumentException(
                'Invalid response given: response string should be terminated by &#92;r&#92;n'
            );
        }

        $response = trim($response);
        if (!preg_match('/^(\d{3}) (.+)$/s', $response, $matches)) {
            throw new InvalidArgumentException(
                sprintf('Invalid response given: "%s"', $response)
            );
        }

        if ($matches[1] < 100 || $matches[1] >= 600) {
            throw new RuntimeException(
                sprintf('Invalid status code: %d', $matches[1])
            );
        }

        return new self((int) $matches[1], $matches[2]);
    }

    /**
     * @param int    $statusCode
     * @param string $message
     */
    public function __construct($statusCode, $message)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s [%d]', $this->message, $this->statusCode);
    }
}
