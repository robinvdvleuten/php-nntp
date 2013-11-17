<?php

namespace Rvdv\Nntp\Response;

interface ResponseInterface
{
    // Authorization constants
    const AUTHORIZATION_REQUIRED        = 450;
    const AUTHORIZATION_CONTINUE        = 350;
    const AUTHORIZATION_ACCEPTED        = 250;
    const AUTHORIZATION_REJECTED        = 452;

    // Authentication constants
    const AUTHENTICATION_REQUIRED       = 480;
    const AUTHENTICATION_CONTINUE       = 381;
    const AUTHENTICATION_ACCEPTED       = 281;
    const AUTHENTICATION_REJECTED       = 482;

    function getMessage();

    function getStatusCode();
}
