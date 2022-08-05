<?php

namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\TestCase;

class IntervalTest extends TestCase
{
    public function testIntervalOfNow()
    {
        $parser = new Parser();
        $tree = $parser->parse('updated_at < NOW');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'updated_at',
                    ],
                    'operator' => '<',
                    'value' => [
                        'IntervalExpression' => [
                            'duration' => 0,
                            'type' => null,
                        ],
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    /**
     * Tests a simple expression: 'value = 5'
     */
    public function testIntervalExpression()
    {
        $parser = new Parser();
        $tree = $parser->parse('updated_at < NOW -2 DAY');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'updated_at',
                    ],
                    'operator' => '<',
                    'value' => [
                        'IntervalExpression' => [
                            'duration' => -2.0,
                            'type' => 'DAY',
                        ],
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }

    public function testPositiveIntervalExpression()
    {
        $parser = new Parser();
        $tree = $parser->parse('updated_at < NOW +2 HOUR');
        $this->assertEquals([
            'logical' => 'AND',
            'expressions' => [
                [
                    'field' => [
                        'ColumnField' => 'updated_at',
                    ],
                    'operator' => '<',
                    'value' => [
                        'IntervalExpression' => [
                            'duration' => 2.0,
                            'type' => 'HOUR',
                        ],
                    ],
                    'logical' => 'AND',
                ],
            ],
        ], $tree->toArray());
    }
}
