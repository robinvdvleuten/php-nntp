<?php

namespace Rvdv\Nntp\Response;

interface ResponseInterface
{
    // Connection constants
    const CONNECTION_CLOSED            = 205;

    // Authentication constants
    const AUTHENTICATION_ACCEPTED      = 281;
    const AUTHENTICATION_CONTINUE      = 381;
    const AUTHENTICATION_REJECTED      = 481;
    const AUTHENTICATION_OUTOFSEQUENCE = 482;

    // Group constants
    const GROUP_SELECTED               = 211;
    const NO_SUCH_GROUP                = 411;

    function getMessage();

    function getStatusCode();
}
