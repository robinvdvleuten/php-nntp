<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robinvdvleuten@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Tests\Command;

/**
 * CommandTest.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExpectedResponseCodeHandlersAreCallable()
    {
        $command = $this->createCommandInstance();

        foreach ($command->getExpectedResponseCodes() as $responseCode => $callable) {
            $this->assertTrue(is_callable([$command, $callable]),
                '->getExpectedResponseCodes() should return callable response code handlers'
            );
        }
    }

    public function testItExpectsAllResponseCodesFromRFC()
    {
        $command = $this->createCommandInstance();

        $this->assertEmpty(array_diff($this->getRFCResponseCodes(), array_keys($command->getExpectedResponseCodes())),
            '->getExpectedResponseCodes() should return all response codes as described by the corresponding RFC'
        );
    }

    abstract protected function createCommandInstance();

    abstract protected function getRFCResponseCodes();
}
