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
 * AuthInfoCommand.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class AuthInfoCommand extends Command implements CommandInterface
{
    public const AUTHINFO_USER = 'USER';
    public const AUTHINFO_PASS = 'PASS';

    private string $type;

    private string $value;

    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;

        parent::__construct();
    }

    public function __invoke(): string
    {
        return sprintf('AUTHINFO %s %s', $this->type, $this->value);
    }

    public function onAuthenticationAccepted(Response $response): Response
    {
        return $response;
    }

    public function onPasswordRequired(Response $response): Response
    {
        return $response;
    }

    public function onAuthenticationRejected(Response $response): never
    {
        throw new RuntimeException(sprintf('Authentication failed with given value for type %s', $this->type));
    }

    public function onAuthenticationOutOfSequence(Response $response): never
    {
        throw new RuntimeException(sprintf('Authentication is out of sequence for type %s', $this->type));
    }
}
