<?php

namespace ProcessMaker\Query\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use ProcessMaker\Query\Traits\PMQL;

class TestRecord extends Model
{
    use PMQL;

    protected $casts = [
        'data' => 'array',
    ];
}
