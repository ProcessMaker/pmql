<?php
namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\TestCase;
use ProcessMaker\Query\Tests\Models\TestRecord;

class TraitTest extends TestCase
{
    /**
     * Check to see if syntax errors are thrown
     */
    public function testSyntaxErrorIsThrown()
    {
        // Note the extra double quote at the end
        $this->expectException(SyntaxError::class);
        $results = TestRecord::pmql("data.first_name = \"Taylor\"\"")->get();
    }

    public function testJsonDataCheck()
    {
        $results = TestRecord::pmql("data.first_name = \"Taylor\"")->get();
        $this->assertCount(1, $results);
        $results = TestRecord::pmql("data.first_name = \"Invalid\"")->get();
        $this->assertCount(0, $results);
    }

    public function testPMQLChained()
    {
        $results = TestRecord::where('id', 1)->pmql("data.first_name = \"Taylor\"")->get();
        $this->assertCount(1, $results);
        $results = TestRecord::where('id', 1)->pmql("data.first_name = \"NoMatch\"")->get();
        $this->assertCount(0, $results);
    }

    public function testPMQLAgainstRegularColumn()
    {
        $results = TestRecord::pmql('id = 1')->get();
        $this->assertCount(1, $results);
    }

    public function testPMQLWithOverriddenExpressionHandler()
    {
        $results = TestRecord::pmql('fakeid = 1', function($query, $field, $op, $value) {
            // Let's rewrite query from fakeid to id
            if($field['value'] == 'fakeid') {
                return $query->where('id', $op['value'], $value['value']);
            }
            // We don't want to handle it, let PMQL trait handle it
            return null;
        })->get();
        $this->assertCount(1, $results);
    }

    public function testPMQLWithANDClause()
    {
        $results = TestRecord::pmql("id = 1 AND id = 2")->get();
        $this->assertCount(1, $results);
    }

}
