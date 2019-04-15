<?php
namespace ProcessMaker\Query;

abstract class BaseExpression
{
    protected $logical;

    const AND = 'AND';
    const OR = 'OR';

    public function __construct($logical)
    {
        $this->logical = $logical;
    }

    public function setLogical($logical)
    {
        $this->logical = $logical;
    }

    public function logical()
    {
        return $this->logical;
    }
}