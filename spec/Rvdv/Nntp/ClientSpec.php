<?php

namespace spec\Rvdv\Nntp;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Client');
    }
}
