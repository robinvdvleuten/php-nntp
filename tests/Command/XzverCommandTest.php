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

use Rvdv\Nntp\Command\XzverCommand;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class XzverCommandTest extends CommandTest
{
    public function testItExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertTrue($command->isMultiLine());
    }

    public function testItExpectsCompressedResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertTrue($command->isCompressed());
    }

    public function testItHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertCount(11, $command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XZVER 1-11', $command->execute());
    }

    public function testItReceivesAResultWhenOverviewInformationFollowsResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\MultiLineResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $lines = [
            "123456789\tRe: Are you checking out NNTP?\trobinvdvleuten@example.com (\"Robin van der Vleuten\")\tSat,3 Aug 2013 13:19:22 -0000\t<nntp123456789@nntp>\t<nntp987654321@nntp>\t321\t123\tXref: nntp:123456789",
        ];

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue($lines));

        $command->onOverviewInformationFollows($response);

        $result = $command->getResult();
        $this->assertCount(11, $result);

        $result->rewind();
        $article = $result->current();

        $this->assertEquals('123456789', $article['number']);
        $this->assertEquals('Re: Are you checking out NNTP?', $article['subject']);
        $this->assertEquals('robinvdvleuten@example.com ("Robin van der Vleuten")', $article['from']);
        $this->assertEquals('Sat,3 Aug 2013 13:19:22 -0000', $article['date']);
        $this->assertEquals('<nntp123456789@nntp>', $article['message_id']);
        $this->assertEquals('<nntp987654321@nntp>', $article['references']);
        $this->assertEquals('321', $article['bytes']);
        $this->assertEquals('123', $article['lines']);
        $this->assertEquals('nntp:123456789', $article['xref']);
    }

    public function testItErrorsWhenNoNewsGroupCurrentSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onNoNewsGroupCurrentSelected($response);
            $this->fail('->onNoNewsGroupCurrentSelected() throws a Rvdv\Nntp\Exception\RuntimeException because a group must be selected first before getting an overview');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onNoNewsGroupCurrentSelected() because a group must be selected first before getting an overview');
        }
    }

    public function testItErrorsWhenNoArticlesSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onNoArticlesSelected($response);
            $this->fail('->onNoArticlesSelected() throws a Rvdv\Nntp\Exception\RuntimeException because no articles selected in the given range');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onNoArticlesSelected() because no articles selected in the given range');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new XzverCommand(1, 11, [
            'subject'    => false,
            'from'       => false,
            'date'       => false,
            'message_id' => false,
            'references' => false,
            'bytes'      => false,
            'lines'      => false,
            'xref'       => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRFCResponseCodes()
    {
        return [
            Response::OVERVIEW_INFORMATION_FOLLOWS,
            Response::NO_NEWSGROUP_CURRENT_SELECTED,
            Response::NO_ARTICLES_SELECTED,
        ];
    }
}
