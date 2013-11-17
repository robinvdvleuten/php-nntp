<?php

namespace Rvdv\Nntp;

use Rvdv\Nntp\Connection\ConnectionInterface;

interface ClientInterface
{
    function connect($host, $port, $secure = false, $timeout = 15);

    function disconnect();
}
