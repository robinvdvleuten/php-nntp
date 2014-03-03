<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * GroupCommand
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class GroupCommand extends Command implements CommandInterface
{
    /**
     * @var string
     */
    private $group;

    /**
     * Constructor
     *
     * @param string $group The name of the group.
     */
    public function __construct($group)
    {
        $this->group = $group;

        parent::__construct(array());
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
    public function getExpectedResponseCodes()
    {
        return array(
            Response::GROUP_SELECTED => 'onGroupSelected',
            Response::NO_SUCH_GROUP => 'onNoSuchGroup',
        );
    }

    public function onGroupSelected(Response $response)
    {
        $message = $response->getMessage();
        $this->result = array_combine(array('count', 'first', 'last', 'name'), explode(' ', $message));
    }

    public function onNoSuchGroup(Response $response)
    {
        throw new RuntimeException(sprintf('A group with name %s does not exists on server', $this->group));
    }
}
