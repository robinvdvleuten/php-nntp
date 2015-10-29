<?php
namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\Response;

class XpathCommand extends Command implements CommandInterface
{
    /**
     * @var int
     */
    const FOUND_PATH = 223;

    /**
     * @var int
     */
    const INVALID_REFERENCE = 501;

    /**
     * @var string
     */
    private $reference;

    /**
     * Constructor.
     *
     * @param string $reference The reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;

        parent::__construct(array());
    }

    /**
     * @return string
     */
    public function execute()
    {
        return sprintf('XPATH %s', $this->reference);
    }

    /**
     * @return array
     */
    public function getExpectedResponseCodes()
    {
        return array(
            self::FOUND_PATH        => 'onFoundPath',
            self::INVALID_REFERENCE => 'onInvalidMessage',
        );
    }

    /**
     * Return the message's reference
     *
     * @param Response $response
     */
    public function onFoundPath(Response $response)
    {
        $this->result = $response->getMessage();
    }

    /**
     * If we didn't find the message, just return an empty response
     *
     * @param Response $response
     */
    public function onInvalidMessage(Response $response)
    {
        $this->result = null;
    }
}
