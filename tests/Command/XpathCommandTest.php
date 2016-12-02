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

use Rvdv\Nntp\Command\XpathCommand;

class XpathCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XPATH <CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>', $command());
    }

    public function testItCanReturnResults()
    {
        $command = $this->createCommandInstance();
        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
                         ->disableOriginalConstructor()
                         ->getMock();

        $response->expects($this->once())
                 ->method('getMessage')
                 ->will($this->returnValue('1'));

        $this->assertEquals('1', $command->onFoundPath($response));
    }

    public function testItReturnsNothingIfNoFoundPath()
    {
        $command = $this->createCommandInstance();
        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
                         ->disableOriginalConstructor()
                         ->getMock();

        $response->expects($this->never())
                 ->method('getMessage')
                 ->will($this->returnValue('501 invalid msgid'));

        $this->assertNull($command->onInvalidMessage($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new XpathCommand('<CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>');
    }
}
