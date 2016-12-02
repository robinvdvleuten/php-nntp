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

use Rvdv\Nntp\Command\PostArticleCommand;

/**
 * PostArticleCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class PostArticleCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals("From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\n\r\nbody", $command());
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

    public function testItReceivesResponseAsResultWhenArticleReceivedResponse()
    {
        $command = $this->createCommandInstance();

        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame($response, $command->onArticleReceived($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new PostArticleCommand('php.doc', 'subject', 'body', 'from <user@example.com>', null);
    }
}
