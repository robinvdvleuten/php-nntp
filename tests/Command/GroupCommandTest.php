<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\GroupCommand;
use Rvdv\Nntp\Response\Response;

/**
 * GroupCommandTest.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class GroupCommandTest extends CommandTest
{
    public function testItNotExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isMultiLine());
    }

    public function testItNotExpectsCompressedResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isCompressed());
    }

    public function testItHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertEmpty($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('GROUP php.doc', $command->execute());
    }

    public function testItReceivesAResultWhenGroupSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue('1234 3000234 3002322 php.doc'));

        $command->onGroupSelected($response);
        $result = $command->getResult();

        $this->assertEquals('1234', $result['count']);
        $this->assertEquals('3000234', $result['first']);
        $this->assertEquals('3002322', $result['last']);
        $this->assertEquals('php.doc', $result['name']);
    }

    public function testItErrorsWhenAuthenticationRejectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onNoSuchGroup($response);
            $this->fail('->onNoSuchGroup() throws a Rvdv\Nntp\Exception\RuntimeException because the group does not exists');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onNoSuchGroup() because the group does not exists');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new GroupCommand('php.doc');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::GROUP_SELECTED,
            Response::NO_SUCH_GROUP,
        );
    }
}
