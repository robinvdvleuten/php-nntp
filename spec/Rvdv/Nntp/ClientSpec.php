<?php

namespace spec\Rvdv\Nntp;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rvdv\Nntp\Command\CommandInterface;
use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\ResponseInterface;

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

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_connect_with_a_nntp_server(ConnectionInterface $connection, ResponseInterface $response)
    {
        $connection->connect('news.php.net', 119, false, 15)->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->connect('news.php.net', 119)->shouldReturnAnInstanceOf('Rvdv\Nntp\Response\ResponseInterface');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_disconnect_from_an_established_connection(ConnectionInterface $connection, CommandInterface $command)
    {
        $connection->disconnect()->willReturn(true)->shouldBeCalled();
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\QuitCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->disconnect()->shouldReturn($command);
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_return_command_when_calling_authinfo(ConnectionInterface $connection, CommandInterface $command)
    {
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\AuthInfoCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->authinfo('USER', 'username')->shouldReturn($command);
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_return_command_when_calling_quit(ConnectionInterface $connection, CommandInterface $command)
    {
        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\QuitCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->quit()->shouldReturn($command);
    }

    public function it_throws_an_exception_when_calling_unknown_command()
    {
        $this->shouldThrow(new \InvalidArgumentException("Given command string 'unknown' is mapped to a non-callable command class (Rvdv\Nntp\Command\UnknownCommand)."))->during('__call', array('unknown', array()));
    }

     /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Command\CommandInterface $command
     */
    public function it_should_be_possible_to_enable_compression(ConnectionInterface $connection, CommandInterface $command)
    {
        $command->getResult()->willReturn(true)->shouldBeCalled();

        $connection->sendCommand(Argument::type('Rvdv\Nntp\Command\XFeatureCommand'))->willReturn($command)->shouldBeCalled();
        $this->setConnection($connection);

        $this->enableCompression()->shouldBe(true);
    }
}
