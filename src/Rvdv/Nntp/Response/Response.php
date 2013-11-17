<?php

namespace Rvdv\Nntp\Response;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $statusCode;

    public static function createFromString($response)
    {
        if (false === strpos($response, "\r\n")) {
            throw new \InvalidArgumentException(
                'Invalid response given: response string should be terminated by &#92;r&#92;n'
            );
        }

        $response = trim($response);
        if (!preg_match('/^(\d{3}) (.+)$/s', $response, $matches)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid response given: "%s"', $response)
            );
        }

        if ($matches[1] < 100 || $matches[1] >= 600) {
            throw new \RuntimeException(
                sprintf('Invalid status code: %d', $matches[1])
            );
        }

        return new self($matches[1], $matches[2]);
    }

    public function __construct($statusCode, $message)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    function getStatusCode()
    {
        return $this->statusCode;
    }
}
