<?php

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\QuitCommand;
use Rvdv\Nntp\Response\Response;

/**
 * QuitCommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class QuitCommandTest extends CommandTest
{
    public function testItNotExpectsMultilineResponses()
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isMultiLine());
    }

    public function testItNotHasDefaultResult()
    {
        $command = $this->createCommandInstance();
        $this->assertNull($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('QUIT', $command->execute());
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
     * {@inheritDoc}
     */
    protected function createCommandInstance()
    {
        return new QuitCommand();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::CONNECTION_CLOSING,
        );
    }
}
