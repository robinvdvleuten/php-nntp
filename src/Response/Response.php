<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
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
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class Response implements ResponseInterface
{
    /**
     * @var array
     */
    public static $codes = [
        'HelpTextFollows' => 100, // RFC 3977

        'PostingAllowed' => 200, // RFC 3977
        'PostingProhibited' => 201, // RFC 3977

        'ConnectionClosing' => 205, // rfc 3977
        'GroupSelected' => 211, // rfc 3977
        'InformationFollows' => 215, // rfc 2980
        'ArticleFollows' => 220, //rfc 3977
        'BodyFollows' => 222, //rfc 3977
        'OverviewInformationFollows' => 224, // rfc 2980
        'ArticleReceived' => 240, //rfc 3977
        'AuthenticationAccepted' => 281, // rfc 4643
        'XfeatureEnabled' => 290,

        'SendArticle' => 340, // rfc 3977
        'PasswordRequired' => 381, // rfc 4643

        'NoSuchGroup' => 411, // rfc 3977
        'NoNewsgroupCurrentSelected' => 412, // rfc 2980
        'NoArticlesSelected' => 420, // rfc 2980
        'NoSuchArticleNumber' => 423, // rfc 3977
        'NoSuchArticleId' => 430, // rfc 3977
        'PostingNotPermitted' => 440, // rfc 3977
        'PostingFailed' => 441, // rfc 3977
        'AuthenticationRejected' => 481, // rfc 4643
        'AuthenticationOutOfSequence' => 482, // rfc 4643

        'CommandUnknown' => 500, // rfc 3977
        'InvalidKeyword' => 501, // rfc 3977
        'CommandUnavailable' => 502, // rfc 4643
        'ProgramError' => 503, // rfc 2980
    ];

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
     * Constructor.
     *
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
