<?php

namespace Rvdv\Nntp\Connection;

interface ConnectionInterface
{
    function connect($host, $port, $secure = false);
}
