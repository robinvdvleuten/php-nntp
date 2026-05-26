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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Command\PostArticleCommand;

/**
 * PostArticleCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class PostArticleCommandTest extends TestCase
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
        $this->assertEquals("From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\n\r\nbody", $command());
    }

    /**
     * @return array<int, array{string, string}>
     */
    public static function getBodyLinesForDotStuffing(): array
    {
        return [
            ['.', '..'],
            ['.hidden', '..hidden'],
            ['..already', '...already'],
            ['not.a.leading.dot', 'not.a.leading.dot'],
        ];
    }

    #[DataProvider('getBodyLinesForDotStuffing')]
    public function testItDotStuffsBodyLinesBeforeSending(string $body, string $expectedBody): void
    {
        $command = new PostArticleCommand('php.doc', 'subject', $body, 'from <user@example.com>', null);

        $this->assertEquals(
            "From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\n\r\n".$expectedBody,
            $command()
        );
    }

    /**
     * @return array<int, array{string, string}>
     */
    public static function getBodiesWithNonCanonicalLineEndings(): array
    {
        return [
            ["first\nsecond", "first\r\nsecond"],
            ["first\rsecond", "first\r\nsecond"],
            ["first\r\nsecond", "first\r\nsecond"],
            ["first\nsecond\rthird\r\nfourth", "first\r\nsecond\r\nthird\r\nfourth"],
        ];
    }

    #[DataProvider('getBodiesWithNonCanonicalLineEndings')]
    public function testItNormalizesBodyLineEndings(string $body, string $expectedBody): void
    {
        $command = new PostArticleCommand('php.doc', 'subject', $body, 'from <user@example.com>', null);

        $this->assertEquals(
            "From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\n\r\n".$expectedBody,
            $command()
        );
    }

    public function testItNormalizesMultilineHeadersBeforeSending(): void
    {
        $command = new PostArticleCommand('php.doc', 'subject', 'body', 'from <user@example.com>', "Organization: Example\nX-Test: yes");

        $this->assertEquals(
            "From: from <user@example.com>\r\nNewsgroups: php.doc\r\nSubject: subject\r\nX-poster: php-nntp\r\nOrganization: Example\r\nX-Test: yes\r\n\r\nbody",
            $command()
        );
    }

    public function testItErrorsWhenPostingFailedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        try {
            $command->onPostingFailed($response);
            $this->fail('->onPostingFailed() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated the post has failed');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onPostingFailed() throws a Rvdv\Nntp\Exception\RuntimeException because the server indicated the post has failed');
        }
    }

    public function testItReceivesResponseAsResultWhenArticleReceivedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $this->assertSame($response, $command->onArticleReceived($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): PostArticleCommand
    {
        return new PostArticleCommand('php.doc', 'subject', 'body', 'from <user@example.com>', null);
    }
}
