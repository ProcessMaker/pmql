<?php
namespace ProcessMaker\Query;

abstract class BaseField
{
    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    public function toEloquent()
    {
        return $this->field;
    }

    public function field()
    {
        return $this->field;
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function toArray()
    {
        return [
            'BaseField' => $this->field
        ];
    }

}