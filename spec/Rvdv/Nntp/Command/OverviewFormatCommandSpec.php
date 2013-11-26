<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OverviewFormatCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\OverviewFormatCommand');
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
        $this->execute()->shouldBe('LIST OVERVIEW.FMT');
    }

    public function it_should_have_a_result()
    {
        $this->getResult()->shouldBeArray();
    }
}
