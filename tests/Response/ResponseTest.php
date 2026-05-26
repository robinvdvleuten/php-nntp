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

use PHPUnit\Framework\TestCase;
use Rvdv\Nntp\Response\Response;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class ResponseTest extends TestCase
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

    public function testItErrorsWhenIncorrectlyTerminatedStringGiven()
    {
        $this->expectException(\Rvdv\Nntp\Exception\InvalidArgumentException::class);

        Response::createFromString('200 server ready - posting allowed');
    }

    public function testItErrorsWhenIncorrectlyFormattedStringGiven()
    {
        $this->expectException(\Rvdv\Nntp\Exception\InvalidArgumentException::class);

        Response::createFromString("server ready - posting allowed 200\r\n");
    }

    public function testItErrorsWhenIncorrectStatusCodeIsFound()
    {
        $this->expectException(\Rvdv\Nntp\Exception\RuntimeException::class);

        Response::createFromString("010 server ready - posting allowed\r\n");
    }
}
