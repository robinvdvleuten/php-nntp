<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\XFeatureCommand;
use Rvdv\Nntp\Response\Response;

/**
 * XFeatureCommandTest.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class XFeatureCommandTest extends CommandTest
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

    public function testItHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XFEATURE COMPRESS GZIP', $command->execute());
    }

    public function testItReceivesAResultWhenXFeatureEnabledResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $command->onXFeatureEnabled($response);

        $this->assertTrue($command->getResult());
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new XFeatureCommand(XFeatureCommand::COMPRESS_GZIP);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::XFEATURE_ENABLED,
        );
    }
}
