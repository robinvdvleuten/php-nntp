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
use Rvdv\Nntp\Command\QuitCommand;

/**
 * QuitCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class QuitCommandTest extends TestCase
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
        $this->assertEquals('QUIT', $command());
    }

    public function testItNotReceivesAResultWhenConnectionClosingResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $command->onConnectionClosing($response);
        $this->addToAssertionCount(1);
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): QuitCommand
    {
        return new QuitCommand();
    }
}
