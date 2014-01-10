<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Command\CommandInterface;

/**
 * ClientInterface
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
interface ClientInterface
{
    function connect();

    function disconnect();

    function authInfo($type, $value);

    function group($name);

    function overview($from, $to, array $format);

    function overviewFormat();

    function quit();

    function xfeature($feature);

    function sendCommand(CommandInterface $command);
}
