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
use Rvdv\Nntp\Command\OverviewFormatCommand;

/**
 * OverviewFormatCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class OverviewFormatCommandTest extends TestCase
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
        $this->assertEquals('LIST OVERVIEW.FMT', $command());
    }

    public function testItReceivesAResultWhenInformationFollowsResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\MultiLineResponse::class);

        $lines = ['Subject:', 'From:', 'Date:', 'Message-ID:', 'References:', 'Bytes:', 'Lines:', 'Xref:full'];

        $response->expects($this->once())
            ->method('getLines')
            ->willReturn($lines);

        $result = $command->onInformationFollows($response);

        $this->assertCount(8, $result);

        $this->assertEmpty(array_diff_assoc($result, [
            'subject' => false,
            'from' => false,
            'date' => false,
            'message_id' => false,
            'references' => false,
            'bytes' => false,
            'lines' => false,
            'xref' => true,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): OverviewFormatCommand
    {
        return new OverviewFormatCommand();
    }
}
