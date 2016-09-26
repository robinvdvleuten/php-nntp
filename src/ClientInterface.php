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
    /**
     * Establish a connection with the NNTP server.
     *
     * @return ResponseInterface
     */
    public function connect();

    /**
     * Disconnect the connection with the NNTP server.
     */
    public function disconnect();

    /**
     * Authenticate with the given username/password.
     *
     * @param string      $username
     * @param string|null $password
     *
     * @return ResponseInterface
     */
    public function authenticate($username, $password = null);

    /**
     * Connect and optionally authenticate with the NNTP server if
     * a username and/or password are given.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return ResponseInterface
     */
    public function connectAndAuthenticate($username = null, $password = null);

    /**
     * Send the AUTHINFO command.
     *
     * @param $type
     * @param $value
     *
     * @return \Rvdv\Nntp\Command\AuthInfoCommand
     */
    public function authInfo($type, $value);

    /**
     * Send the BODY command.
     *
     * @param $article
     *
     * @return \Rvdv\Nntp\Command\BodyCommand
     */
    public function body($article);

    /**
     * Send the HELP command.
     *
     * @return \Rvdv\Nntp\Command\HelpCommand
     */
    public function help();

    /**
     * Send the LIST command.
     *
     * @param string|null $keyword
     * @param string|null $arguments
     *
     * @return \Rvdv\Nntp\Command\GroupCommand
     */
    public function listGroups($keyword = null, $arguments = null);

    /**
     * Send the GROUP command.
     *
     * @param $name
     *
     * @return \Rvdv\Nntp\Command\GroupCommand
     */
    public function group($name);

    /**
     * Send the LIST OVERVIEW.FMT command.
     *
     * @return \Rvdv\Nntp\Command\OverviewFormatCommand
     */
    public function overviewFormat();

    /**
     * Post an article to usenet.
     *
     * @param string      $groups
     * @param string      $subject
     * @param string      $body
     * @param string      $from
     * @param string|null $headers
     *
     * @return ResponseInterface
     */
    public function post($groups, $subject, $body, $from, $headers = null);

    /**
     * Send the article.
     *
     * @param string      $groups
     * @param string      $subject
     * @param string      $body
     * @param string      $from
     * @param string|null $headers
     *
     * @return ResponseInterface
     */
    public function postArticle($groups, $subject, $body, $from, $headers = null);

    /**
     * Send the QUIT command.
     *
     * @return \Rvdv\Nntp\Command\QuitCommand
     */
    public function quit();

    /**
     * Send the XFEATURE command.
     *
     * @param $feature
     *
     * @return \Rvdv\Nntp\Command\XFeatureCommand
     */
    public function xfeature($feature);

    /**
     * Send the XOVER command.
     *
     * @param $from
     * @param $to
     * @param array $format
     *
     * @return \Rvdv\Nntp\Command\XoverCommand
     */
    public function xover($from, $to, array $format);

    /**
     * Send the XZVER command.
     *
     * @param $from
     * @param $to
     * @param array $format
     *
     * @return \Rvdv\Nntp\Command\XzverCommand
     */
    public function xzver($from, $to, array $format);

    /**
     * Send the given command to the NNTP server.
     *
     * @param CommandInterface $command
     *
     * @return CommandInterface
     */
    public function sendCommand(CommandInterface $command);
}
