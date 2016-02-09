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

use Rvdv\Nntp\Command\PostCommand;
use Rvdv\Nntp\Response\Response;

/**
 * PostCommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class PostCommandTest extends CommandTest
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
        $this->assertEmpty($command->getResult());
    }

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('POST', $command->execute());
    }
	
	public function testItErrorsWhenPostingNotPermittedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onPostingNotPermitted($response);
            $this->fail('->onPostingNotPermitted() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated posting is not permitted');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onPostingNotPermitted() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated posting is not permitted');
        }
    }
    
    public function testItNotReceivesAResultWhenSendArticleResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $command->onSendArticle($response);

        $this->assertEmpty($command->getResult());
    }

    /**
     * {@inheritDoc}
     */
    protected function createCommandInstance()
    {
        return new PostCommand();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRFCResponseCodes()
    {
        return array(
            Response::SEND_ARTICLE,
            Response::POSTING_NOT_PERMITTED,
        );
    }
}
