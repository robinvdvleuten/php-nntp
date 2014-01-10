<?php

namespace Rvdv\Nntp\Tests\Connection;

use Rvdv\Nntp\Connection\Connection;

/**
 * ConnectionTest
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
}
