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
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;
use Rvdv\Nntp\Response\ResponseInterface;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
class MultiLineResponseTest extends TestCase
{
    public function testItReturnsMessageAndStatusCodeFromInjectedResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue('server ready - posting allowed'));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $multiLineResponse = new MultiLineResponse($response, []);

        $this->assertEquals('server ready - posting allowed', $multiLineResponse->getMessage());
        $this->assertEquals(200, $multiLineResponse->getStatusCode());
    }

    public function testItReturnsInjectedResponseAsString(): void
    {
        $response = Response::createFromString("200 server ready - posting allowed\r\n");

        $multiLineResponse = new MultiLineResponse($response, []);

        $this->assertEquals('server ready - posting allowed [200]', (string) $multiLineResponse);
    }
}
