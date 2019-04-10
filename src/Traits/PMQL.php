<?php
namespace ProcessMaker\Query\Traits;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\Processor;

/**
 * This trait can be "use" by any Eloquent model, allowing the pmql scope to be applied to query building
 */
trait PMQL
{
    // Scope to apply to query building for this eloquent model
    public function scopePMQL($builder, $query, $customCallback = null)
    {
        $parser = new Parser;
        $tree = $parser->parse($query);

        return Processor::process($builder, $tree, $customCallback);
    }

}
