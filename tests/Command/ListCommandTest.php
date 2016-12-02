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

use Rvdv\Nntp\Command\ListCommand;
use Rvdv\Nntp\Response\Response;

/**
 * GroupCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ListCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals('LIST', $command());

        $command = $this->createCommandInstance(ListCommand::KEYWORD_ACTIVE);
        $this->assertEquals('LIST ACTIVE', $command());

        $command = $this->createCommandInstance(ListCommand::KEYWORD_ACTIVE, 'filter');
        $this->assertEquals('LIST ACTIVE filter', $command());
    }

    public function testItReceivesAResultWhenGroupSelectedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\MultiLineResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects($this->once())
            ->method('getLines')
            ->will($this->returnValue([
                'php.announce 0000000174 0000000001 m',
                'php.apc.dev 0000000287 0000000001 y',
                'php.beta 0000000161 0000000001 n',
            ]));

        $result = $command->onListFollows($response);

        $this->assertCount(3, $result);

        $this->assertEquals('php.announce', $result[0]['name']);
        $this->assertEquals('0000000174', $result[0]['high']);
        $this->assertEquals('0000000001', $result[0]['low']);
        $this->assertEquals('m', $result[0]['status']);

        $this->assertEquals('php.apc.dev', $result[1]['name']);
        $this->assertEquals('0000000287', $result[1]['high']);
        $this->assertEquals('0000000001', $result[1]['low']);
        $this->assertEquals('y', $result[1]['status']);

        $this->assertEquals('php.beta', $result[2]['name']);
        $this->assertEquals('0000000161', $result[2]['high']);
        $this->assertEquals('0000000001', $result[2]['low']);
        $this->assertEquals('n', $result[2]['status']);
    }

    public function testItErrorsWhenInvalidKeywordResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onInvalidKeyword($response);
            $this->fail('->onInvalidKeyword() throws a Rvdv\Nntp\Exception\RuntimeException because the keyword is invalid');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onInvalidKeyword() because the keyword is invalid');
        }
    }

    public function testItErrorsFailedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onError($response);
            $this->fail('->onError() throws a Rvdv\Nntp\Exception\RuntimeException the server errored');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onError() because the server errored');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance($keyword = null, $arguments = null)
    {
        return new ListCommand($keyword, $arguments);
    }
}
