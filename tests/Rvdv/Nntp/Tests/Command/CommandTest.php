<?php

namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\CommandInterface;

/**
 * CommandTest
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExpectedResponseCodeHandlersAreCallable()
    {
        $command = $this->createCommandInstance();

        foreach ($command->getExpectedResponseCodes() as $responseCode => $callable) {
            $this->assertTrue(is_callable(array($command, $callable)),
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

    protected abstract function createCommandInstance();

    protected abstract function getRFCResponseCodes();
}
