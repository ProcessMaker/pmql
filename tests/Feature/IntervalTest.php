<?php
namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\TestCase;

class IntervalTest extends TestCase
{
    /**
     * Tests a simple expression: 'value = 5'
     */
    public function testSimpleIntervalExpression()
    {
        $parser = new Parser();
        //$tree = $parser->parse('curdate(), interval 2 day');
        $tree = $parser->parse('"test"');
        dd('here');
        dd($tree);
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
}