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
use Rvdv\Nntp\Command\XpathCommand;

class XpathCommandTest extends TestCase
{
    public function testItReturnsStringWhenExecuting(): void
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XPATH <CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>', $command());
    }

    public function testItCanReturnResults(): void
    {
        $command = $this->createCommandInstance();
        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $response->expects($this->once())
                 ->method('getMessage')
                 ->will($this->returnValue('1'));

        $this->assertEquals('1', $command->onFoundPath($response));
    }

    public function testItReturnsNothingIfNoFoundPath(): void
    {
        $command = $this->createCommandInstance();
        $response = $this->createMock(\Rvdv\Nntp\Response\Response::class);

        $response->expects($this->never())
                 ->method('getMessage')
                 ->will($this->returnValue('501 invalid msgid'));

        $command->onInvalidMessage($response);
        $this->addToAssertionCount(1);
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance(): XpathCommand
    {
        return new XpathCommand('<CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>');
    }
}
