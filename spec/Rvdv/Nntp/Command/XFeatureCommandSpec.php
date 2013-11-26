<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XFeatureCommandSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('feature');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\XFeatureCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    public function it_should_send_group_command()
    {
        $this->execute()->shouldBe('XFEATURE feature');
    }

    public function it_should_have_a_result()
    {
        $this->getResult()->shouldBe(false);
    }

    public function it_should_not_expect_a_multiline_response()
    {
        $this->isMultiLine()->shouldBe(false);
    }
}
