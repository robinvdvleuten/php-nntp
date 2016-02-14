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

use Rvdv\Nntp\Command\PostArticleCommand;
use Rvdv\Nntp\Response\Response;

/**
 * PostArticleCommandTest.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class PostArticleCommandTest extends CommandTest
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
        $this->assertEquals("From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\n\r\nbody", $command->execute());
    }

    public function testItErrorsWhenPostingFailedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $command->onPostingFailed($response);
            $this->fail('->onPostingFailed() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated the post has failed');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onPostingFailed() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated the post has failed');
        }
    }

    public function testItNotReceivesAResultWhenArticleReceivedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $command->onArticleReceived($response);

        $this->assertEmpty($command->getResult());
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new PostArticleCommand('php.doc', 'subject', 'body', 'from <user@example.com>', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRFCResponseCodes()
    {
        return [
            Response::ARTICLE_RECEIVED,
            Response::POSTING_FAILED,
        ];
    }
}
