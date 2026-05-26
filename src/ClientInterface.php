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
     */
    public function authenticate(string $username, ?string $password = null): ResponseInterface;

    /**
     * Connect and optionally authenticate with the server if
     * a username and optional password are given.
     */
    public function connectAndAuthenticate(string $username, ?string $password = null): ResponseInterface;

    /**
     * Send the AUTHINFO command.
     */
    public function authInfo(string $type, string $value): ResponseInterface;

    /**
     * Send the BODY command.
     */
    public function body(string $article): string;

    /**
     * Send the HEAD command.
     */
    public function head(string $article): string;

    /**
     * Send the HELP command.
     */
    public function help(): string;

    /**
     * Send the LIST command.
     *
     * @return array<int, array{name: string, high: string, low: string, status: string}>
     */
    public function listGroups(?string $keyword = null, mixed $arguments = null): array;

    /**
     * Send the GROUP command.
     *
     * @return array{count: string, first: string, last: string, name: string}
     */
    public function group(string $name): array;

    /**
     * Send the LIST OVERVIEW.FMT command.
     *
     * @return array<string, bool>
     */
    public function overviewFormat(): array;

    /**
     * Post an article to usenet.
     */
    public function post(string $groups, string $subject, string $body, string $from, ?string $headers = null): ResponseInterface;

    /**
     * Send the article.
     */
    public function postArticle(string $groups, string $subject, string $body, string $from, ?string $headers = null): ResponseInterface;

    /**
     * Send the QUIT command.
     */
    public function quit(): void;

    /**
     * Send the XFEATURE command.
     */
    public function xfeature(string $feature): bool;

    /**
     * Send the XOVER command.
     *
     * @param array<string, bool> $format
     *
     * @return array<int, array<string, string>>
     */
    public function xover(int $from, int $to, array $format): array;

    /**
     * Send the XZVER command.
     *
     * @param array<string, bool> $format
     *
     * @return array<int, array<string, string>>
     */
    public function xzver(int $from, int $to, array $format): array;

    /**
     * Send the given command to the NNTP server.
     *
     * @return mixed
     */
    public function sendCommand(CommandInterface $command): mixed;
}
