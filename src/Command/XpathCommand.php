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

use Rvdv\Nntp\Response\Response;

class XpathCommand extends Command implements CommandInterface
{
    /**
     * @var int
     */
    public const FOUND_PATH = 223;

    /**
     * @var int
     */
    public const INVALID_REFERENCE = 501;

    private string $reference;

    /**
     * @param string $reference The reference
     */
    public function __construct(string $reference)
    {
        $this->reference = $reference;

        parent::__construct();
    }

    public function __invoke(): string
    {
        return sprintf('XPATH %s', $this->reference);
    }

    /**
     * Return the message's reference.
     */
    public function onFoundPath(Response $response): string
    {
        return $response->getMessage();
    }

    /**
     * If we didn't find the message, just return an empty response.
     */
    public function onInvalidMessage(Response $response): void
    {
        return;
    }
}
