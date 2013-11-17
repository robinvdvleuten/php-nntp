<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthInfoCommandSpec extends ObjectBehavior
{
    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     */
    public function let($connection)
    {
        $this->beConstructedWith($connection, 'USER', 'username');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\AuthInfoCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     */
    public function it_should_send_authinfo_user_command($connection)
    {
        $this->beConstructedWith($connection, 'USER', 'username');
        $this->execute()->shouldBe('AUTHINFO USER username');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     */
    public function it_should_send_authinfo_pass_command($connection)
    {
        $this->beConstructedWith($connection, 'PASS', 'password');
        $this->execute()->shouldBe('AUTHINFO PASS password');
    }
}
