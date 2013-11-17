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

    public function it_should_send_quit_command()
    {
        $this->execute()->shouldBe('QUIT');
    }

    public function it_should_not_have_a_result()
    {
        $this->getResult()->shouldBeNull();
    }
}
