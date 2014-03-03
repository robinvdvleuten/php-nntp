<?php

namespace Rvdv\Nntp\Response;

interface MultiLineResponseInterface extends ResponseInterface
{
    public function getLines();
}
