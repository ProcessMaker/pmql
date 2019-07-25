<?php
namespace ProcessMaker\Query;

use Illuminate\Support\Facades\DB;

class FunctionCall extends BaseField
{
    protected $name;
    protected $params;

    public function __construct($name, $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    public function toArray()
    {
        $params = [];
        foreach($this->params as $param) {
            $params[] = $param->toArray();
        }

        return [
            'FunctionCall' => [
                'name' => $this->name,
                'params' => $params
            ]
        ];
    }

    public function field()
    {
        $params = [];
        foreach($this->params as $param) {
            $params[] = $param->toEloquent();
        }
        return $this->name . "(" . implode(",", $params) . ")";
    }

    public function toEloquent()
    {
        return DB::raw($this->field());

    }

}