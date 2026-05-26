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
use Rvdv\Nntp\Command\HelpCommand;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class HelpCommandTest extends TestCase
{
    public function testItExpectsMultilineResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertTrue($command->isMultiLine());
    }

    public function testItNotExpectsCompressedResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isCompressed());
    }

    public function testItReturnsStringWhenExecuting(): void
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('HELP', $command());
    }

    public function testItReceivesAResultWhenInformationFollowsResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\MultiLineResponse::class);

        $lines = ['body [MessageID|Number]', 'date', 'head [MessageID|Number]', 'help', 'ihave'];

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue($lines));

        $this->assertEquals(implode("\n", $lines), $command->onHelpTextFollows($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): HelpCommand
    {
        return new HelpCommand();
    }
}
