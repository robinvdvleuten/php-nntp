<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Command;

use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Command\GroupCommand;

/**
 * GroupCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class GroupCommandTest extends TestCase
{
    public function testItNotExpectsMultilineResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isMultiLine());
    }

    public function testItNotExpectsCompressedResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isCompressed());
    }

    public function testItReturnsStringWhenExecuting(): void
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('GROUP php.doc', $command());
    }

    public function testItReceivesAResultWhenGroupSelectedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $response->expects($this->once())
            ->method('getMessage')
            ->willReturn('1234 3000234 3002322 php.doc');

        $result = $command->onGroupSelected($response);

        $this->assertEquals('1234', $result['count']);
        $this->assertEquals('3000234', $result['first']);
        $this->assertEquals('3002322', $result['last']);
        $this->assertEquals('php.doc', $result['name']);
    }

    public function testItErrorsWhenAuthenticationRejectedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

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
    protected function createCommandInstance(): GroupCommand
    {
        return new GroupCommand('php.doc');
    }
}
