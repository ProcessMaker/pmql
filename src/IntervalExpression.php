<?php

namespace ProcessMaker\Query;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IntervalExpression
{
    protected $duration;

    protected $type;

    public function __construct($duration = 0, $type = null)
    {
        $this->duration = $duration;
        $this->type = $type;
    }

    /**
     * Magic getter for our fields.
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * Dumps information to array. This can be used for terse testing
     */
    public function toArray()
    {
        return [
            'IntervalExpression' => [
                'duration' => $this->duration,
                'type' => $this->type,
            ],
        ];
    }

    public function toEloquent()
    {
        // Intervals need to be converted to carbon, which Eloquent will then convert
        $val = new Carbon();
        if ($this->duration != 0) {
            $method = 'add' . ucfirst(strtolower($this->type)) . 's';
            $val->$method($this->duration);
        }

        return $val;
    }
}
