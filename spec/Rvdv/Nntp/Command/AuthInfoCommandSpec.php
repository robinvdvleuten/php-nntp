<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthInfoCommandSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('USER', 'username');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\AuthInfoCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    public function it_should_send_authinfo_user_command()
    {
        $this->beConstructedWith('USER', 'username');
        $this->execute()->shouldBe('AUTHINFO USER username');
    }

    public function it_should_send_authinfo_pass_command()
    {
        $this->beConstructedWith('PASS', 'password');
        $this->execute()->shouldBe('AUTHINFO PASS password');
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
