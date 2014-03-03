<?php

namespace Rvdv\Nntp\Response;

interface ResponseInterface
{
    public function getMessage();

    public function getStatusCode();
}
