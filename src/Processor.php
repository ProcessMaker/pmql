<?php
namespace ProcessMaker\Query;

class Processor
{
    private static function isExpr($arr)
    {
        return (count($arr) == 3 && $arr[1]['type'] == 'operator');
    }

    // Perform a Post-Order LRN tree traversal to build query
    // See: https://en.wikipedia.org/wiki/Tree_traversal#Post-order_(LRN)

    // $parseTree top level array is 3 elements:
    // $parseTree[0] = is the left node
    // $parseTree[1] = is the operator
    // $parseTree[2] = is the right node
    public static function process($queryBuilder, $tree, $fieldCallbackOverride = null)
    {
        if (self::isExpr($tree[0])) {
            dd('not impl');
            // Recursive head down to the L node
            self::process($queryBuilder, $tree[0]);
        } else if (self::isExpr($tree[2])) {
            // Handle parameter groupi ng
        } else {
            // This is a simple column <op> value
            if ($fieldCallbackOverride) {
                $fieldCallbackOverride($tree[0], $tree[1], $tree[2]);
            } else {
                // Try and do default behavior
                switch ($tree[0]['type']) {
                    case 'field':
                        $queryBuilder = $queryBuilder->where($tree[0]['value'], $tree[1]['value'], $tree[2]['value']);
                        break;
                    case 'json_field':
                        $field = str_replace('.', '->', $tree[0]['value']);
                        $queryBuilder = $queryBuilder->where($field, $tree[1]['value'], $tree[2]['value']);
                        break;
                    default:
                        throw \Exception("Invalid left node type in query parse.");
                }
            }
        }
        return $queryBuilder;
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

    public static function flatstr($x, $rejectSpace = false, $joinChar = '') {
        return implode($joinChar, self::flatten($x, $rejectSpace, []));
      }
}