<?php
namespace ProcessMaker\Query;

use Iterator;
use ArrayAccess;
use Exception;

/**
 * Represents a list of expressions
 */
class ExpressionCollection extends BaseExpression implements ArrayAccess, Iterator
{

    protected $expressions;

    // Iterator position
    private $_position;

    public function __construct($logical = 'AND')
    {
        parent::__construct($logical);
        $this->expressions = [];
        $this->_position = 0;
    }

    public function offsetSet($offset, $value)
    {
        if(!is_a($value, BaseExpression::class)) {
            throw new Exception("Cannot set a non expression to an ExpressionCollection");
        }
        if (is_null($offset)) {
            // Append
            $this->expressions[] = $value;
        } else {
            $this->expressions[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->expressions[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->expressions[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->expressions[$offset]) ? $this->expressions[$offset] : null;
    }

    public function toArray()
    {
        $expressions = [];
        foreach($this->expressions as $expression) {
            $expressions[] = $expression->toArray();
        }
        return [
            'logical' => $this->logical,
            'expressions' => $expressions
        ];
    }

    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->expressions[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return isset($this->expressions[$this->_position]);
    }

}