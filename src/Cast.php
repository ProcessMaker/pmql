<?php
namespace ProcessMaker\Query;

use Illuminate\Support\Facades\DB;

class Cast extends BaseField
{
    protected $field;
    protected $type;

    public function __construct($field, $type)
    {
        $this->field = $field;
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'Cast' => [
                'field' => $this->field->toArray(),
                'type' => $this->type
            ]
        ];
    }

    public function toEloquent($connection = null)
    {
        return DB::raw('CAST(' . $this->field->toEloquent($connection) . ' AS ' . $this->type . ')');
    }

}