<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Connection\ConnectionInterface;

interface ClientInterface
{
    function authenticate($username, $password);

    function connect($host, $port, $secure = false, $timeout = 15);

    function disconnect();

    function enableCompression();
}
