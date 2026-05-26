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

use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Command\PostCommand;

/**
 * PostCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class PostCommandTest extends TestCase
{
    public function testItNotExpectsMultilineResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isMultiLine());
    }

    public function testItNotExpectsCompressedResponses(): void
    {
        $command = $this->createCommandInstance();
        $this->assertFalse($command->isCompressed());
    }

    public function testItReturnsStringWhenExecuting(): void
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('POST', $command());
    }

    public function testItErrorsWhenPostingNotPermittedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        try {
            $command->onPostingNotPermitted($response);
            $this->fail('->onPostingNotPermitted() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated posting is not permitted');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onPostingNotPermitted() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated posting is not permitted');
        }
    }

    public function testItNotReceivesAResultWhenSendArticleResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $command->onSendArticle($response);
        $this->addToAssertionCount(1);
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): PostCommand
    {
        return new PostCommand();
    }
}
