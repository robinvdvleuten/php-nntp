<?php

namespace spec\Rvdv\Nntp;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Client');
    }

    public function it_is_initializable_through_factory_method()
    {
        $this::create()->shouldReturnAnInstanceOf('Rvdv\Nntp\Client');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_connects_with_a_nntp_server($connection, $response)
    {
        $connection->connect('news.php.net', 119, false, 15)->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->connect('news.php.net', 119)->shouldReturnAnInstanceOf('Rvdv\Nntp\Response\ResponseInterface');
    }
}
