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

use Rvdv\Nntp\Command\XFeatureCommand;

/**
 * XFeatureCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class XFeatureCommandTest extends \PHPUnit_Framework_TestCase
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

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XFEATURE COMPRESS GZIP', $command());
    }

    public function testItReceivesAResultWhenXFeatureEnabledResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($command->onXFeatureEnabled($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new XFeatureCommand(XFeatureCommand::COMPRESS_GZIP);
    }
}
