<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Response;

use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testItCreatesResponseInstanceFromString()
    {
        $response = Response::createFromString("200 server ready - posting allowed\r\n");

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testItReturnsResponseAsString()
    {
        $this->assertEquals('server ready - posting allowed [200]', (string) Response::createFromString("200 server ready - posting allowed\r\n"));
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\InvalidArgumentException
     */
    public function testItErrorsWhenIncorrectlyTerminatedStringGiven()
    {
        Response::createFromString('200 server ready - posting allowed');
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\InvalidArgumentException
     */
    public function testItErrorsWhenIncorrectlyFormattedStringGiven()
    {
        Response::createFromString("server ready - posting allowed 200\r\n");
    }

    /**
     * @expectedException \Rvdv\Nntp\Exception\RuntimeException
     */
    public function testItErrorsWhenIncorrectStatusCodeIsFound()
    {
        Response::createFromString("010 server ready - posting allowed\r\n");
    }
}
