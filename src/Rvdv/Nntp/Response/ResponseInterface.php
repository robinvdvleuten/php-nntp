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

    // Overview constants
    const OVERVIEW_FOLLOWS             = 224;
    const NO_GROUP_SELECTED            = 412;
    const CURRENT_ARTICLE_INVALID      = 420;
    const NO_ARTICLES_IN_RANGE         = 423;
    const NO_ARTICLE_WITH_ID           = 430;

    function getMessage();

    function getStatusCode();
}
