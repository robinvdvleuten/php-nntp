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
    const FOUND_PATH = 223;

    /**
     * @var int
     */
    const INVALID_REFERENCE = 501;

    /**
     * @var string
     */
    private $reference;

    /**
     * Constructor.
     *
     * @param string $reference The reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;

        parent::__construct([]);
    }

    /**
     * @return string
     */
    public function execute()
    {
        return sprintf('XPATH %s', $this->reference);
    }

    /**
     * Return the message's reference.
     *
     * @param Response $response
     */
    public function onFoundPath(Response $response)
    {
        $this->result = $response->getMessage();
    }

    /**
     * If we didn't find the message, just return an empty response.
     *
     * @param Response $response
     */
    public function onInvalidMessage(Response $response)
    {
        $this->result = null;
    }
}
