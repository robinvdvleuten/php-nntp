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
use Rvdv\Nntp\Command\AuthInfoCommand;

/**
 * AuthInfoCommandTest.
 *
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class AuthInfoCommandTest extends TestCase
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
        $command = $this->createCommandInstance(AuthInfoCommand::AUTHINFO_USER, 'user');
        $this->assertEquals('AUTHINFO USER user', $command());

        $command = $this->createCommandInstance(AuthInfoCommand::AUTHINFO_PASS, 'pass');
        $this->assertEquals('AUTHINFO PASS pass', $command());
    }

    public function testItReceivesResponseAsResultWhenAuthenticatedAcceptedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $this->assertSame($response, $command->onAuthenticationAccepted($response));
    }

    public function testItReceivesResponseAsResultWhenPasswordRequiredResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $this->assertSame($response, $command->onPasswordRequired($response));
    }

    public function testItErrorsWhenAuthenticationRejectedResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        try {
            $command->onAuthenticationRejected($response);
            $this->fail('->onAuthenticationRejected() throws a Rvdv\Nntp\Exception\RuntimeException because the authentication is rejected');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onAuthenticationRejected() throws a Rvdv\Nntp\Exception\RuntimeException because the authentication is rejected');
        }
    }

    public function testItErrorsWhenAuthenticationOutOfSequenceResponse(): void
    {
        $command = $this->createCommandInstance();

        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        try {
            $command->onAuthenticationOutOfSequence($response);
            $this->fail('->onAuthenticationOutOfSequence() throws a Rvdv\Nntp\Exception\RuntimeException because the authentication is out of sequence');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->onAuthenticationOutOfSequence() throws a Rvdv\Nntp\Exception\RuntimeException because the authentication is out of sequence');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): AuthInfoCommand
    {
        $args = func_get_args();
        if (empty($args)) {
            $args = [AuthInfoCommand::AUTHINFO_USER, 'user'];
        }

        return new AuthInfoCommand($args[0], $args[1]);
    }
}
