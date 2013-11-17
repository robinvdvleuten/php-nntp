<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;

class AuthInfoCommand extends Command implements CommandInterface
{
    const AUTHINFO_USER = 'USER';
    const AUTHINFO_PASS = 'PASS';

    public function __construct(ConnectionInterface $connection, $type, $value)
    {
        $this->type = $type;
        $this->value = $value;

        parent::__construct($connection);
    }

    public function __toString()
    {
        return sprintf('AUTHINFO %s %s', $this->type, $this->value);
    }
}
