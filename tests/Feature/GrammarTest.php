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
            [
                [
                    'type' => 'field',
                    'value' => 'value'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => 5.0
                ]
            ]
        ], $tree);
    }

    public function testSimpleExpressionWithNestedField()
    {
        $parser = new Parser();
        $tree = $parser->parse('data.customer.name = "Taylor"');
        $this->assertEquals([
            [
                [
                    'type' => 'nested_field',
                    'value' => 'data.customer.name'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => "Taylor"
                ]
            ]
        ], $tree);
    }

    public function testSimpleExpressionWithNestedFieldArray()
    {
        $parser = new Parser();
        $tree = $parser->parse('data.customer.orders[0].name = "Taylor"');
        $this->assertEquals([
            [
                [
                    'type' => 'nested_field',
                    'value' => 'data.customer.orders[0].name'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => "Taylor"
                ]
            ]
        ], $tree);
    }


    public function testSimpleExpressionWithString()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = "test"');
        $this->assertEquals([
            [
                [
                    'type' => 'field',
                    'value' => 'value'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => 'test'
                ]
            ]
        ], $tree);
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
            [
                [
                    [
                        'type' => 'field',
                        'value' => 'value'
                    ],
                    [
                        'type' => 'operator',
                        'value' => '='
                    ],
                    [
                        'type' => 'literal',
                        'value' => 5
                    ]
                ]
            ]
        ], $tree);
    }

    public function testGroupedExpression()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = 5 AND foo = "baz" AND cat = "dog"');
        $this->assertEquals([
            [
                [
                    'type' => 'field',
                    'value' => 'value'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => 5
                ]
            ],
            [
                'group_operator' => [
                    'type' => 'operator',
                    'value' => 'AND'
                ],
                'expression' => [
                    [
                        'type' => 'field',
                        'value' => 'foo'
                    ],
                    [
                        'type' => 'operator',
                        'value' => '='
                    ],
                    [
                        'type' => 'literal',
                        'value' => 'baz'
                    ]
                ]
            ],
            [
                'group_operator' => [
                    'type' => 'operator',
                    'value' => 'AND'
                ],
                'expression' => [
                    [
                        'type' => 'field',
                        'value' => 'cat'
                    ],
                    [
                        'type' => 'operator',
                        'value' => '='
                    ],
                    [
                        'type' => 'literal',
                        'value' => 'dog'
                    ]
                ]
            ]
        ], $tree);
    }

    public function testGroupedExpressionInsideOr()
    {
        $parser = new Parser();
        $tree = $parser->parse('value = 5 OR (foo = "baz" AND cat = "dog")');
        $this->assertEquals([
            [
                [
                    'type' => 'field',
                    'value' => 'value'
                ],
                [
                    'type' => 'operator',
                    'value' => '='
                ],
                [
                    'type' => 'literal',
                    'value' => 5
                ]
            ],
            [
                'group_operator' => [
                    'type' => 'operator',
                    'value' => 'OR'
                ],
                'expression' => [
                    [
                        [
                            'type' => 'field',
                            'value' => 'foo'
                        ],
                        [
                            'type' => 'operator',
                            'value' => '='
                        ],
                        [
                            'type' => 'literal',
                            'value' => 'baz'
                        ]
                    ],
                    [
                        'group_operator' => [
                            'type' => 'operator',
                            'value' => 'AND'
                        ],
                        'expression' => [
                            [
                                'type' => 'field',
                                'value' => 'cat'
                            ],
                            [
                                'type' => 'operator',
                                'value' => '='
                            ],
                            [
                                'type' => 'literal',
                                'value' => 'dog'
                            ]
                        ]
                    ]
                ]
            ]
        ], $tree);
    }

    public function testTwoGroupedExpressionsJoined()
    {
        $parser = new Parser();
        $tree = $parser->parse('(value = 5 OR value2 = 10) OR (foo = "baz" AND cat = "dog")');
        $this->assertEquals(
            array (
                0 =>
                array (
                  0 =>
                  array (
                    0 =>
                    array (
                      'type' => 'field',
                      'value' => 'value',
                    ),
                    1 =>
                    array (
                      'type' => 'operator',
                      'value' => '=',
                    ),
                    2 =>
                    array (
                      'type' => 'literal',
                      'value' => 5.0,
                    ),
                  ),
                  1 =>
                  array (
                    'group_operator' =>
                    array (
                      'type' => 'operator',
                      'value' => 'OR',
                    ),
                    'expression' =>
                    array (
                      0 =>
                      array (
                        'type' => 'field',
                        'value' => 'value2',
                      ),
                      1 =>
                      array (
                        'type' => 'operator',
                        'value' => '=',
                      ),
                      2 =>
                      array (
                        'type' => 'literal',
                        'value' => 10.0,
                      ),
                    ),
                  ),
                ),
                1 =>
                array (
                  'group_operator' =>
                  array (
                    'type' => 'operator',
                    'value' => 'OR',
                  ),
                  'expression' =>
                  array (
                    0 =>
                    array (
                      0 =>
                      array (
                        'type' => 'field',
                        'value' => 'foo',
                      ),
                      1 =>
                      array (
                        'type' => 'operator',
                        'value' => '=',
                      ),
                      2 =>
                      array (
                        'type' => 'literal',
                        'value' => 'baz',
                      ),
                    ),
                    1 =>
                    array (
                      'group_operator' =>
                      array (
                        'type' => 'operator',
                        'value' => 'AND',
                      ),
                      'expression' =>
                      array (
                        0 =>
                        array (
                          'type' => 'field',
                          'value' => 'cat',
                        ),
                        1 =>
                        array (
                          'type' => 'operator',
                          'value' => '=',
                        ),
                        2 =>
                        array (
                          'type' => 'literal',
                          'value' => 'dog',
                        ),
                      ),
                    ),
                  ),
                ),
              )
            ,$tree
        );
    }
}
