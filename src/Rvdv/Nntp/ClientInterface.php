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
     * @return Rvdv\Nntp\Response\ResponseInterface A response containing the welcome message of the server.
     */
    function connect();

    /**
     * Disconnect the connection with the NNTP server.
     *
     * @return bool A boolean indicating if the connection is disconnected.
     */
    function disconnect();

    function authInfo($type, $value);

    function group($name);

    function overview($from, $to, array $format);

    function overviewFormat();

    function quit();

    function xfeature($feature);

    function sendCommand(CommandInterface $command);
}
