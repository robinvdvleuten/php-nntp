<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QuitCommandSpec extends ObjectBehavior
{
    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     */
    public function let($connection)
    {
        $this->beConstructedWith($connection);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\QuitCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_send_quit_command($connection, $response)
    {
        $connection->sendCommand('QUIT')->willReturn($response)->shouldBeCalled();

        $this->execute()->shouldReturnAnInstanceOf('Rvdv\Nntp\Response\ResponseInterface');
    }
}
