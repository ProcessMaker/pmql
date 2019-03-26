<?php
namespace ProcessMaker\Query;

use ProcessMaker\Query\Facades\QueryManager;
use ProcessMaker\Query\Parser;

class Query extends Parser
{
    protected $manager;

    public function __construct(QueryManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Pass in optional options as a secondary parameter
     */
    public function parse($input)
    {
        // Our overriden parse, could be used to do some setup/teardown
        // For now, let's call our parser
        return parent::parse($input);
    }

    /**
     * Can be used by methods in the grammar to get access to the manager
     */
    protected function manager()
    {
        return $this->manager;
    }

}