<?php
namespace ProcessMaker\Query;

class Processor
{
    protected $tree;
    protected $callback; 

    public function __construct($tree, $callback = null)
    {
        $this->tree = $tree;
        $this->callback = $callback;
    }

    public function process($builder)
    {
        $method = $this->tree->logicalMethod();
        return $builder->$method($this->processCollection($this->tree));
    }

    // Perform a Post-Order LRN tree traversal to build query
    // See: https://en.wikipedia.org/wiki/Tree_traversal#Post-order_(LRN)

    private function processCollection($collection)
    {
        return function($builder) use($collection) {
            foreach($collection as $expression) {
                $method = $expression->logicalMethod();
                if(is_a($expression, ExpressionCollection::class)) {
                    $builder->$method($this->processCollection($expression));
                } else {
                    if($this->callback) {
                        $func = ($this->callback)($expression);
                        if(is_callable($func)) {
                            $builder->$method(($this->callback)($expression));
                        } else {
                            $this->addToBuilder($builder, $method, $expression);
                        }
                    } else {
                        $this->addToBuilder($builder, $method, $expression);
                    }
                }
            }
            return $builder;
        };
    }

    private function addToBuilder(&$builder, $method, $expression)
    {
        if ($expression->value instanceof ArrayValue) {
            $method = $expression->operator === Expression::OPERATOR_IN ? 'whereIn' : 'whereNotIn';
            $builder->$method(
                $expression->field->toEloquent(),
                $expression->value->toEloquent()
            );
        } else {
            $builder->$method(
                $expression->field->toEloquent(),
                $expression->operator,
                $expression->value->toEloquent()
            );
        }
    }

    public static function append($arr, $x)
    {
        $arr[] = $x;
        return $arr;
    }

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function flatten($x, $rejectSpace = false, $acc = [])
    {
        // We're going to check for various types of $x and handle them differently
        // Null?
        if ($x == null) {
            if (!$rejectSpace) {
                // We want to keep the whitespace/null, so append x to acc
                return self::append($acc, $x);
            }
            return $acc;
        }
        // Associative array?
        if (is_array($x) && self::isAssoc($x)) {
            return self::append($acc, $x);
        }
        // Is it an empty array, or is a string with nothing but whitespace and we're rejecting?
        if ($rejectSpace && (
            (is_string($x) && preg_match('/^\s*$/', $x)) || (is_array($x) && count($x) == 0))) {
            return $acc;
        }
        // Is it a string? If so, just append
        if (is_string($x)) {
            return self::append($acc, $x);
        }
        // Is it a numeric array? Let's just flatten
        for ($i = 0; $i < count($x); $i++) {
            $acc = self::flatten($x[$i], $rejectSpace, $acc);
        }
        return $acc;
    }

    public static function flatstr($x, $rejectSpace = false, $joinChar = '')
    {
        return implode($joinChar, self::flatten($x, $rejectSpace, []));
    }
}
