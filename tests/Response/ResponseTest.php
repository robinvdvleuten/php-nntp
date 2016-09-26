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

        $this->assertInstanceOf('Rvdv\Nntp\Response\Response', $response);
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testItReturnsResponseAsString()
    {
        $this->assertEquals('server ready - posting allowed [200]', (string) Response::createFromString("200 server ready - posting allowed\r\n"));
    }

    public function testItErrorsWhenIncorrectlyTerminatedStringGiven()
    {
        try {
            Response::createFromString('200 server ready - posting allowed');
            $this->fail('::createFromString() throws a Rvdv\Nntp\Exception\InvalidArgumentException because the string is incorrectly terminated');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\InvalidArgumentException', $e, '::createFromString() throws a Rvdv\Nntp\Exception\InvalidArgumentException because the string is incorrectly terminated');
        }
    }

    public function testItErrorsWhenIncorrectlyFormattedStringGiven()
    {
        try {
            Response::createFromString("server ready - posting allowed 200\r\n");
            $this->fail('::createFromString() throws a Rvdv\Nntp\Exception\InvalidArgumentException because the string is incorrectly formatted');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\InvalidArgumentException', $e, '::createFromString() throws a Rvdv\Nntp\Exception\InvalidArgumentException because the string is incorrectly formatted');
        }
    }

    public function testItErrorsWhenIncorrectStatusCodeIsFound()
    {
        try {
            Response::createFromString("010 server ready - posting allowed\r\n");
            $this->fail('::createFromString() throws a Rvdv\Nntp\Exception\RuntimeException because status code is less than 100');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '::createFromString() throws a Rvdv\Nntp\Exception\RuntimeException because status code is less than 100');
        }

        try {
            Response::createFromString("700 server ready - posting allowed\r\n");
            $this->fail('::createFromString() throws a Rvdv\Nntp\Exception\RuntimeException because status code is greater than 600');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '::createFromString() throws a Rvdv\Nntp\Exception\RuntimeException because status code is greater than 600');
        }
    }
}
