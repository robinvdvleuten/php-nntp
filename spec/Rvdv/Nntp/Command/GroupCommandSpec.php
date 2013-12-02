<?php

namespace spec\Rvdv\Nntp\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rvdv\Nntp\Response\ResponseInterface;

class GroupCommandSpec extends ObjectBehavior
{
    public function getMatchers()
    {
        return array(
            'containGroupValues' => function($subject, $values) {
                $diff = array_diff($subject, $values);
                return empty($diff);
            },
        );
    }

    public function let()
    {
        $this->beConstructedWith('groupname');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Rvdv\Nntp\Command\GroupCommand');
    }

    public function it_implements_interface()
    {
        $this->shouldImplement('Rvdv\Nntp\Command\CommandInterface');
    }

    public function it_should_send_group_command()
    {
        $this->execute()->shouldBe('GROUP groupname');
    }

    public function it_should_have_a_result()
    {
        $this->getResult()->shouldBeArray();
    }

    public function it_should_not_expect_a_multiline_response()
    {
        $this->isMultiLine()->shouldBe(false);
    }

    /**
     * @param Rvdv\Nntp\Response\ResponseInterface $response
     */
    public function it_should_have_result_when_response_received(ResponseInterface $response)
    {
        $group = array('count' => '1234', 'first' => '3000234', 'last' => '3002322', 'name' => 'misc.test');
        $response->getMessage()->willReturn(implode(' ', array_values($group)))->shouldBeCalled();
        $this->handleGroupResponse($response);

        $this->getResult()->shouldContainGroupValues($group);
    }
}
