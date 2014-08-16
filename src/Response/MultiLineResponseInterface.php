<?php

namespace Rvdv\Nntp\Response;

interface MultiLineResponseInterface extends ResponseInterface
{
    /**
     * @return \ArrayAccess
     */
    public function getLines();
}
