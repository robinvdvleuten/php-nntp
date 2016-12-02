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

use Rvdv\Nntp\Command\QuitCommand;
use Rvdv\Nntp\Response\Response;

/**
 * QuitCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class QuitCommandTest extends \PHPUnit_Framework_TestCase
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

    public function testItNotHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertNull($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('QUIT', $command());
    }

    public function testItNotReceivesAResultWhenConnectionClosingResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $command->onConnectionClosing($response);

        $this->assertNull($command->getResult());
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new QuitCommand();
    }
}
