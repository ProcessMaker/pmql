<?php
namespace ProcessMaker\Query;

use Illuminate\Support\Facades\DB;

class FunctionCall extends BaseField
{
    protected $name;
    protected $param;

    public function __construct($name, $param)
    {
        $this->name = $name;
        $this->param = $param;
    }

    public function toArray()
    {
        return [
            'FunctionCall' => [
                'name' => $this->name,
                'param' => $this->param->toArray()
            ]
        ];
    }

    public function field()
    {
        return $this->name . "(" . $this->param->toEloquent() . ")";
    }

    public function toEloquent()
    {
        return DB::raw($this->field());

    }

}