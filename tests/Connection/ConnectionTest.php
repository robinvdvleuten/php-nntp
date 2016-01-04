<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Connection;

use Rvdv\Nntp\Connection\Connection;

/**
 * ConnectionTest.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectionCanBeEstablishedThroughSocket()
    {
        $connection = new Connection('localhost', 5000);
        $response = $connection->connect();

        $this->assertInstanceof('Rvdv\Nntp\Response\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('server ready - posting allowed', $response->getMessage());
    }

    public function testErrorIsThrownWhenConnectionCannotBeEstablished()
    {
        $connection = new Connection('unknownhost', 3000);

        try {
            $connection->connect();
            $this->fail('->connect() throws a Rvdv\Nntp\Exception\RuntimeException because the connection cannot be established');
        } catch (\Exception $e) {
            $this->assertInstanceof('Rvdv\Nntp\Exception\RuntimeException', $e, '->connect() throws a Rvdv\Nntp\Exception\RuntimeException because the connection cannot be established');
        }
    }
}
