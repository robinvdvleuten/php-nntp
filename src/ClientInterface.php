<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
interface ClientInterface
{
    public function connect(): ResponseInterface;

    public function disconnect(): void;

    /**
     * Authenticate with the given username/password.
     *
     * @return ResponseInterface
     */
    public function authenticate(string $username, ?string $password = null): ResponseInterface;

    /**
     * Connect and optionally authenticate with the server if
     * a username and optional password are given.
     *
     * @return ResponseInterface
     */
    public function connectAndAuthenticate(string $username, ?string $password = null): ResponseInterface;

    /**
     * Send the AUTHINFO command.
     *
     * @return mixed
     */
    public function authInfo(string $type, string $value): mixed;

    /**
     * Send the BODY command.
     *
     * @return mixed
     */
    public function body(string $article): mixed;

    /**
     * Send the HELP command.
     *
     * @return mixed
     */
    public function help(): mixed;

    /**
     * Send the LIST command.
     *
     * @return mixed
     */
    public function listGroups(?string $keyword = null, mixed $arguments = null): mixed;

    /**
     * Send the GROUP command.
     *
     * @return mixed
     */
    public function group(string $name): mixed;

    /**
     * Send the LIST OVERVIEW.FMT command.
     *
     * @return mixed
     */
    public function overviewFormat(): mixed;

    /**
     * Post an article to usenet.
     *
     * @return ResponseInterface
     */
    public function post(string $groups, string $subject, string $body, string $from, ?string $headers = null): ResponseInterface;

    /**
     * Send the article.
     *
     * @return mixed
     */
    public function postArticle(string $groups, string $subject, string $body, string $from, ?string $headers = null): mixed;

    /**
     * Send the QUIT command.
     *
     * @return mixed
     */
    public function quit(): mixed;

    /**
     * Send the XFEATURE command.
     *
     * @return mixed
     */
    public function xfeature(string $feature): mixed;

    /**
     * Send the XOVER command.
     *
     * @param array<string, bool> $format
     *
     * @return mixed
     */
    public function xover(int $from, int $to, array $format): mixed;

    /**
     * Send the XZVER command.
     *
     * @param array<string, bool> $format
     *
     * @return mixed
     */
    public function xzver(int $from, int $to, array $format): mixed;

    /**
     * Send the given command to the NNTP server.
     *
     * @return mixed
     */
    public function sendCommand(CommandInterface $command): mixed;
}
