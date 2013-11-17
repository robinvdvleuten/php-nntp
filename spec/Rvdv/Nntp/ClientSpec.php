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
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_disconnect_from_an_established_connection($connection, $response)
    {
        $connection->disconnect()->shouldBeCalled();
        $connection->sendCommand('QUIT')->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->disconnect()->shouldReturnAnInstanceOf('Rvdv\Nntp\Command\QuitCommand');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_return_command_when_calling_authinfo($connection, $response)
    {
        $connection->sendCommand('AUTHINFO USER username')->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->authInfo('USER', 'username')->shouldReturnAnInstanceOf('Rvdv\Nntp\Command\AuthInfoCommand');
    }

    /**
     * @param Rvdv\Nntp\Connection\ConnectionInterface $connection
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_return_command_when_calling_quit($connection, $response)
    {
        $connection->sendCommand('QUIT')->willReturn($response)->shouldBeCalled();
        $this->setConnection($connection);

        $this->quit()->shouldReturnAnInstanceOf('Rvdv\Nntp\Command\QuitCommand');
    }
}
