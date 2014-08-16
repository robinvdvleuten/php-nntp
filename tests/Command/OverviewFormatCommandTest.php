<?php

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\OverviewFormatCommand;
use Rvdv\Nntp\Response\Response;

/**
 * OverviewFormatCommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class OverviewFormatCommandTest extends CommandTest
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
        $this->assertEquals('LIST OVERVIEW.FMT', $command->execute());
    }

    public function testItReceivesAResultWhenInformationFollowsResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\MultiLineResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $lines = array('Subject:', 'From:', 'Date:', 'Message-ID:', 'References:', 'Bytes:', 'Lines:', 'Xref:full');

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue($lines));

        $command->onInformationFollows($response);

        $result = $command->getResult();
        $this->assertCount(8, $result);

        $this->assertEmpty(array_diff_assoc($result, array(
            'subject' => false,
            'from' => false,
            'date' => false,
            'message_id' => false,
            'references' => false,
            'bytes' => false,
            'lines' => false,
            'xref' => true,
        )));
    }

    /**
     * {@inheritDoc}
     */
    protected function createCommandInstance()
    {
        return new OverviewFormatCommand();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::INFORMATION_FOLLOWS,
        );
    }
}
