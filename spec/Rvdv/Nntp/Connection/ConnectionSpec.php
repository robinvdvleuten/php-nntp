<?php

namespace spec\Rvdv\Nntp\Connection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConnectionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Connection\Connection');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Connection\ConnectionInterface');
    }

    public function it_connects_with_a_nntp_server()
    {
        $this->connect('news.php.net', 119)->shouldReturnAnInstanceOf('Rvdv\Nntp\Response\ResponseInterface');
    }
}
