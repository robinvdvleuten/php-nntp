<?php

namespace Rvdv\Nntp;

interface ClientInterface
{
    function authenticate($username, $password);

    function connect($host, $port, $secure = false, $timeout = 15);

    function disconnect();

    function enableCompression();
}
