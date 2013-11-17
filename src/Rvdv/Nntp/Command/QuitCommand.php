<?php

namespace Rvdv\Nntp\Command;

class QuitCommand extends Command implements CommandInterface
{
    public function __toString()
    {
        return 'QUIT';
    }
}
