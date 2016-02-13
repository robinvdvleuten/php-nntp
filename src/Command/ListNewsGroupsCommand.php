<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Response\Response;

/**
 * ListNewsGroupsCommand
 *
 * @author warlord
 */
class ListNewsGroupsCommand extends Command implements CommandInterface
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct(array(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return 'LIST NEWSGROUPS';
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return array(
            Response::INFORMATION_FOLLOWS => 'onListNewsGroups'
        );
    }
    
    /**
    * Return the list of Newsgroups as an array 
    *
    * @param MultiLineReponse
    *
    * @return array
    */
    public function onListNewsGroups(MultiLineResponse $response)
    {
        $this->result = (array) $response->getLines();
    }
}
