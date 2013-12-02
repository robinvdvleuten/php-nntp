<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OverviewCommandSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(10, 10, array());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\OverviewCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    public function it_should_expect_a_multiline_response()
    {
        $this->isMultiLine()->shouldBe(true);
    }

    public function it_should_send_xover_command()
    {
        $this->execute()->shouldBe('XOVER 10-10');
    }
}
