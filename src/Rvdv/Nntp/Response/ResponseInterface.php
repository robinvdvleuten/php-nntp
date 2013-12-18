<?php

namespace Rvdv\Nntp\Response;

interface ResponseInterface
{
    function getMessage();

    function getStatusCode();
}
