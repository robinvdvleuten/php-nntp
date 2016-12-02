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

use Rvdv\Nntp\Command\OverviewFormatCommand;
use Rvdv\Nntp\Response\Response;

/**
 * OverviewFormatCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class OverviewFormatCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testItExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertTrue($command->isMultiLine());
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
        $this->assertEquals('LIST OVERVIEW.FMT', $command());
    }

    public function testItReceivesAResultWhenInformationFollowsResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\MultiLineResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $lines = ['Subject:', 'From:', 'Date:', 'Message-ID:', 'References:', 'Bytes:', 'Lines:', 'Xref:full'];

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue($lines));

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
    protected function createCommandInstance()
    {
        return new OverviewFormatCommand();
    }
}
