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

/**
 * MultiLineResponse.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class MultiLineResponse implements MultiLineResponseInterface
{
    /**
     * @var array<int, string>
     */
    private array $lines;

    private ResponseInterface $response;

    /**
     * @param array<int, string> $lines
     */
    public function __construct(ResponseInterface $response, array $lines)
    {
        $this->response = $response;
        $this->lines = $lines;
    }

    public function getMessage(): string
    {
        return $this->response->getMessage();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function __toString(): string
    {
        return (string) $this->response;
    }
}
