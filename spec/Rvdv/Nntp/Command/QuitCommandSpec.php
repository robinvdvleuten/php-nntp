<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;

class QuitCommandSpec extends ObjectBehavior
{
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

    public function it_should_not_expect_a_multiline_response()
    {
        $this->isMultiLine()->shouldBe(false);
    }
}
