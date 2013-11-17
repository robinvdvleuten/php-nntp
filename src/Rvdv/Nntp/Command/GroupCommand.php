<?php

namespace Rvdv\Nntp\Command;

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
    private $result = array();

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

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    public function handleGroupResponse(ResponseInterface $response)
    {
        $message = $response->getMessage();
        $this->result = array_combine(array('count', 'first', 'last', 'name'), explode(' ', $message));
    }
}
