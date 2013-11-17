<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Connection\ConnectionInterface;
use Rvdv\Nntp\Response\ResponseInterface;

class GroupCommand extends Command implements CommandInterface
{
    /**
     * @var string
     */
    private $group;

    /**
     * @var array
     */
    private $results = array();

    /**
     * Constructor
     *
     * @param string $group The name of the group.
     */
    public function __construct($group)
    {
        $this->group = $group;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return sprintf('GROUP %s', $this->group);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHandlers()
    {
        return array(
            ResponseInterface::GROUP_SELECTED => 'handleGroupResponse',
        );
    }

    public function getResult()
    {
        return $this->results;
    }

    public function handleGroupResponse(ResponseInterface $response)
    {
        $message = $response->getMessage();
        $this->results = array_combine(array('count', 'first', 'last', 'name'), explode(' ', $message));
    }
}
