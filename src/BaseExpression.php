<?php
namespace ProcessMaker\Query;

abstract class BaseExpression
{
    protected $logical;
    protected $operator;

    const AND = 'AND';
    const OR = 'OR';
    const OPERATOR_IN = 'IN';
    const OPERATOR_NOT_IN = 'NOT IN';

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

    public function logicalMethod()
    {
        return $this->logical == self::OR ? 'orWhere' : 'where';
    }
}