<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\CommandInterface;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
interface ClientInterface
{
    /**
     * Establish a connection with the NNTP server.
     *
     * @return \Rvdv\Nntp\Response\ResponseInterface A response containing the welcome message of the server.
     */
    public function connect();

    /**
     * Disconnect the connection with the NNTP server.
     *
     * @return bool A boolean indicating if the connection is disconnected.
     */
    public function disconnect();

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
     * Send the GROUP command.
     *
     * @param $name
     *
     * @return \Rvdv\Nntp\Command\GroupCommand
     */
    public function group($name);

    /**
     * Send the XOVER command.
     *
     * @param $from
     * @param $to
     * @param array $format
     *
     * @return \Rvdv\Nntp\Command\OverviewCommand
     */
    public function overview($from, $to, array $format);

    /**
     * Send the LIST OVERVIEW.FMT command.
     *
     * @return \Rvdv\Nntp\Command\OverviewFormatCommand
     */
    public function overviewFormat();

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
     * Send the given command to the NNTP server.
     *
     * @param \Rvdv\Nntp\Command\CommandInterface $command
     *
     * @return \Rvdv\Nntp\Command\CommandInterface
     */
    public function sendCommand(CommandInterface $command);
}
