<?php
namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\TestCase;

class GrammarTest extends TestCase
{
    /**
     * Tests a simple expression: 'value = 5'
     */
    public function testSimpleExpressionWithInteger()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = 5');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'value',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 5.0,
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testSimpleExpressionWithNestedField()
    {
        $parser = new Parser();
        $tree = $parser->parse('data.customer.name = "Taylor"');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'JsonField' => 'data.customer.name',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 'Taylor',
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testSimpleExpressionWithNestedFieldArray()
    {
        $parser = new Parser();
        $tree = $parser->parse('data.customer.orders[0].name = "Taylor"');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'JsonField' => 'data.customer.orders[0].name',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 'Taylor',
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testSimpleExpressionWithString()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = "test"');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'value',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 'test',
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testSyntaxErrorThrownWhenUsingInvalidValue()
    {
        $this->expectException(SyntaxError::class);
        $parser = new Parser();
        $tree = $parser->parse('value = test');
    }

    public function testExpressionInParens()
    {
        $parser = new Parser();
        $tree = $parser->parse('(value = 5)');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'logical' => 'AND',
                    'expressions' => [
                        [
                            'field' => [
                                'ColumnField' => 'value',
                            ],
                            'operator' => '=',
                            'value' => [
                                'LiteralValue' => 5.0,
                            ],
                            'logical' => 'AND',
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    public function testGroupedExpression()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = 5 AND foo = "baz" AND cat = "dog"');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'value',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 5.0,
                    ],
                    'logical' => 'AND',
                ],
                [
                    'field' => [
                        'ColumnField' => 'foo',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 'baz',
                    ],
                    'logical' => 'AND',
                ],
                [
                    'field' => [
                        'ColumnField' => 'cat',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 'dog',
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testGroupedExpressionInsideOr()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = 5 OR (foo = "baz" AND cat = "dog")');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'value',
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => 5.0,
                    ],
                    'logical' => 'AND',
                ],
                [
                    'logical' => 'OR',
                    'expressions' => [
                        [
                            'field' => [
                                'ColumnField' => 'foo',
                            ],
                            'operator' => '=',
                            'value' => [
                                'LiteralValue' => 'baz',
                            ],
                            'logical' => 'AND',
                        ],
                        [
                            'field' => [
                                'ColumnField' => 'cat',
                            ],
                            'operator' => '=',
                            'value' => [
                                'LiteralValue' => 'dog',
                            ],
                            'logical' => 'AND',
                        ],
                    ],
                ],
            ],

        ], $tree->toArray());
    }

    public function testTwoGroupedExpressionsJoined()
    {
        $parser = new Parser();
        $tree = $parser->parse('(value = 5 OR value2 = 10) OR (foo = "baz" AND cat = "dog")');
        $this->assertEquals(
            [
                'logical' => 'AND',
                'expressions' => [
                    [
                        'logical' => 'AND',
                        'expressions' => [
                            [
                                'field' => [
                                    'ColumnField' => 'value',
                                ],
                                'operator' => '=',
                                'value' => [
                                    'LiteralValue' => 5.0,
                                ],
                                'logical' => 'AND',
                            ],
                            [
                                'field' => [
                                    'ColumnField' => 'value2',
                                ],
                                'operator' => '=',
                                'value' => [
                                    'LiteralValue' => 10.0,
                                ],
                                'logical' => 'OR',
                            ],
                        ],

                    ],
                    [
                        'logical' => 'OR',
                        'expressions' => [
                            [
                                'field' => [
                                    'ColumnField' => 'foo',
                                ],
                                'operator' => '=',
                                'value' => [
                                    'LiteralValue' => 'baz',
                                ],
                                'logical' => 'AND',
                            ],
                            [
                                'field' => [
                                    'ColumnField' => 'cat',
                                ],
                                'operator' => '=',
                                'value' => [
                                    'LiteralValue' => 'dog',
                                ],
                                'logical' => 'AND',
                            ],
                        ],
                    ],
                ],
            ],
            $tree->toArray()
        );
    }

    public function testQueryWithFunctionCall()
    {
        $parser = new Parser();
        $tree = $parser->parse('date(foo) = "2012-12-12"');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'FunctionCall' => [
                            'name' => 'date',
                            'param' => [
                                'ColumnField' => 'foo',
                            ],
                        ],
                    ],
                    'operator' => '=',
                    'value' => [
                        'LiteralValue' => '2012-12-12',
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testQueryWithCast()
    {
        $parser = new Parser();
        $tree = $parser->parse('cast(data.age as integer) > 25');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'Cast' => [
                            'field' => [
                                'JsonField' => 'data.age'
                            ],
                            'type' => 'integer'
                        ],
                    ],
                    'operator' => '>',
                    'value' => [
                        'LiteralValue' => 25.0,
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());

    }
}
