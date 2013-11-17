<?php

namespace Rvdv\Nntp\Response;

interface MultiLineResponseInterface extends ResponseInterface
{
    function getLines();
}
