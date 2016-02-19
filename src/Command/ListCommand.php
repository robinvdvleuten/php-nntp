<?php
namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Response\MultiLineResponse;
use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

class ListCommand extends Command
{
    const KEYWORD_ACTIVE        = 'ACTIVE';
    const KEYWORD_ACTIVE_TIMES  = 'ACTIVE.TIMES';
    const KEYWORD_DISTRIB_PATS  = 'DISTRIB.PATS';
    const KEYWORD_HEADERS       = 'HEADERS';
    const KEYWORD_NEWSGROUPS    = 'NEWSGROUPS';
    const KEYWORD_OVERVIEW_FMT  = 'OVERVIEW.FMT';

    /**
     * @var string
     */
    protected $keyword;

    /**
     * @var string
     */
    protected $arguments;

    /**
     * @param string $keyword
     * @param string $arguments
     */
    public function __construct($keyword = null, $arguments = null)
    {
        parent::__construct([], true);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponseCodes()
    {
        return [
            Response::INFORMATION_FOLLOWS => 'onListFollows',
            Response::INVALID_KEYWORD => 'onInvalidKeyword',
            Response::PROGRAM_ERROR => 'onError',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return sprintf('LIST %s %s', $this->keyword, $this->arguments);
    }

    /**
     * Called when the list is received from the server
     *
     * @param MultiLineResponse $response
     */
    public function onListFollows(MultiLineResponse $response)
    {
        $lines = $response->getLines();
        $totalLines = count($lines);

        for ($i = 0; $i < $totalLines; ++$i) {
            list($name, $high, $low, $status) = explode(' ', $lines[$i]);

            $this->result[$i] = [
                'name' => $name,
                'high' => $high,
                'low' => $low,
                'status' => $status,
            ];
        }
    }

    public function onInvalidKeyword(Response $response)
    {
        throw new RuntimeException('Invalid keyword, or unexpected argument for keyword.', $response->getStatusCode());
    }

    public function onError(Response $response)
    {
        throw new RuntimeException('Error retrieving group list', $response->getStatusCode());
    }
}
