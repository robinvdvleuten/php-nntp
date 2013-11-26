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

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\ClientInterface');
    }

    public function it_should_be_initializable_through_create_method()
    {
        $this::create()->shouldReturnAnInstanceOf('Rvdv\Nntp\Client');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_connect_with_a_nntp_server($connection, $response)
    {
        $connection->connect('news.php.net', 119, false, 15)->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->connect('news.php.net', 119)->shouldReturnAnInstanceOf('Rvdv\Nntp\Response\ResponseInterface');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_disconnect_from_an_established_connection($connection, $command)
    {
        $connection->disconnect()->shouldBeCalled();
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\QuitCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->disconnect()->shouldReturn($command);
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_return_command_when_calling_authinfo($connection, $command)
    {
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\AuthInfoCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->authinfo('USER', 'username')->shouldReturn($command);
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_return_command_when_calling_quit($connection, $command)
    {
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\QuitCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->quit()->shouldReturn($command);
    }

    public function it_throws_an_exception_when_calling_unknown_command()
    {
        $this->shouldThrow(new \InvalidArgumentException("Given command string 'unknown' is mapped to a non-callable command class (Rvdv\Nntp\Command\UnknownCommand)."))->during('__call', array('unknown', array()));
    }
}
