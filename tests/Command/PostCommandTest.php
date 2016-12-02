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

use Rvdv\Nntp\Command\PostCommand;

/**
 * PostCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class PostCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals('POST', $command());
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

        $this->assertEmpty($command->onSendArticle($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new PostCommand();
    }
}
