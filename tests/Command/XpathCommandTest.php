<?php
namespace Rvdv\Nntp\Tests\Command;

use Rvdv\Nntp\Command\XpathCommand;

class XpathCommandTest extends CommandTest
{

    public function testItReturnsStringWhenExecuting()
    {
        $command = $this->createCommandInstance();
        $this->assertEquals('XPATH <CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>', $command->execute());
    }

    public function testItCanReturnResults()
    {
        $command  = $this->createCommandInstance();
        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
                         ->disableOriginalConstructor()
                         ->getMock();

        $response->expects($this->once())
                 ->method('getMessage')
                 ->will($this->returnValue('1'));

        $command->onFoundPath($response);
        $result = $command->getResult();
        $this->assertEquals('1', $result);
    }

    public function testItReturnsNothingIfNoFoundPath()
    {
        $command  = $this->createCommandInstance();
        $response = $this->getMockBuilder('Rvdv\Nntp\Response\Response')
                         ->disableOriginalConstructor()
                         ->getMock();

        $response->expects($this->never())
                 ->method('getMessage')
                 ->will($this->returnValue('501 invalid msgid'));

        $command->onInvalidMessage($response);
        $result = $command->getResult();
        $this->assertNull($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommandInstance()
    {
        return new XpathCommand('<CAFp73XuG9PfYv448muxijyk7MS5xG7J5zxz021YQPtAReYvkyQ@mail.gmail.com>');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRFCResponseCodes()
    {
        return [
            XpathCommand::FOUND_PATH,
            XpathCommand::INVALID_REFERENCE,
        ];
    }
}
