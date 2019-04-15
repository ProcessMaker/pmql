<?php
namespace ProcessMaker\Query;

abstract class BaseValue
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function toEloquent()
    {
        return $this->value();
    }
}