<?php

namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\ColumnField;
use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\Models\TestRecord;
use ProcessMaker\Query\Tests\TestCase;

class TraitTest extends TestCase
{
    /**
     * Check to see if syntax errors are thrown
     */
    public function testSyntaxErrorIsThrown()
    {
        // Note the extra double quote at the end
        $this->expectException(SyntaxError::class);
        $results = TestRecord::pmql('data.first_name = "Taylor""')->get();
    }

    public function testSimpleExpression()
    {
        $builder = TestRecord::pmql('foo = "bar"');
        $this->assertEquals('select * from "test_records" where ("foo" = ?)', $builder->toSql());
        $this->assertEquals([
            'bar',
        ], $builder->getBindings());
    }

    public function testLikeExpression()
    {
        $builder = TestRecord::pmql('foo LIKE "ba%"');
        $this->assertEquals('select * from "test_records" where ("foo" LIKE ?)', $builder->toSql());
        $this->assertEquals([
            'ba%',
        ], $builder->getBindings());
    }

    public function testSimpleGroupedExpression()
    {
        $builder = TestRecord::pmql('foo = "bar" AND cat = "dog"');
        $this->assertEquals('select * from "test_records" where ("foo" = ? and "cat" = ?)', $builder->toSql());
        $this->assertEquals([
            'bar',
            'dog',
        ], $builder->getBindings());
    }

    public function testQuery()
    {
        $results = TestRecord::pmql('data.first_name = "Taylor"')->get();
        $this->assertCount(1, $results);
    }

    public function testNoMatchQuery()
    {
        $results = TestRecord::pmql('data.first_name = "Invalid"')->get();
        $this->assertCount(0, $results);
    }

    public function testLikeQueryWithMatch()
    {
        $results = TestRecord::pmql('data.first_name LIKE "Ta%"')->get();
        $this->assertCount(1, $results);
    }

    public function testMatchWithGrouping()
    {
        $results = TestRecord::pmql('data.first_name = "Taylor" OR data.first_name = "Alan"')->get();
        $this->assertCount(2, $results);
    }

    /**
     * This tests the callback for dealing with callbacks to handle overwriting expression handling
     */
    public function testQueryWithOverride()
    {
        $results = TestRecord::pmql('invalidid = 1', function ($expression) {
            if (is_a($expression->field, ColumnField::class) && $expression->field->field() == 'invalidid') {
                return function ($builder) use ($expression) {
                    $builder->{$expression->logicalMethod()}('id', $expression->operator, $expression->value->toEloquent());
                };
            }
        })->get();
        $this->assertCount(1, $results);
    }

    public function testQueryWithFunctionCall()
    {
        $results = TestRecord::pmql('data.first_name = "TAYLOR"')->get();
        // Should not match with a full uppercase name
        $this->assertCount(0, $results);
        $results = TestRecord::pmql('upper(data.first_name) = "TAYLOR"')->get();
        $this->assertCount(1, $results);
    }

    public function testQueryWithCast()
    {
        $results = TestRecord::pmql('cast(data.age as number) > 40')->get();
        $this->assertCount(0, $results);
        $results = TestRecord::pmql('cast(data.age as number) < 40')->get();
        // Should retrieve all of our records
        $this->assertCount(5, $results);
        $results = TestRecord::pmql('cast(data.age as number) < 35')->get();
        $this->assertCount(2, $results);
    }

    public function testQueryWithInterval()
    {
        $results = TestRecord::pmql('updated_at < NOW -2 DAY')->get();
        $this->assertCount(0, $results);
        $results = TestRecord::pmql('updated_at < NOW +2 DAY')->get();
        // Should retrieve all of our records, since they've all been updated before now + 2 day
        $this->assertCount(5, $results);
        // Sleep for a second, so we can be sure to be greater than when the records were created
        sleep(1);
        $results = TestRecord::pmql('created_at < NOW')->get();
        // Should retrieve all of our records, since they've all been updated before now
        $this->assertCount(5, $results);
    }

    public function testInExpression()
    {
        $builder = TestRecord::pmql('foo IN ["abc",123, "def"]');
        $this->assertEquals('select * from "test_records" where ("foo" in (?, ?, ?))', $builder->toSql());
        $this->assertEquals(['abc', 123, 'def'], $builder->getBindings());
    }

    public function testNotInExpression()
    {
        $assert = function ($pmql, $bindings) {
            $builder = TestRecord::pmql($pmql);
            $subs = substr(str_repeat('?, ', count($bindings)), 0, -2);
            $this->assertEquals('select * from "test_records" where ("foo" not in (' . $subs . '))', $builder->toSql());
            $this->assertEquals($bindings, $builder->getBindings());
        };
        $assert('foo NOT IN ["abc",123, "def"]', ['abc', 123, 'def']);
        $assert('foo NOT IN ["abc"]', ['abc']);
        $assert('foo NOT IN [1]', [1]);
    }

    public function testInvalidOperatorOnArray()
    {
        try {
            TestRecord::pmql('foo > ["abc",123, "def"]');
        } catch (SyntaxError $e) {
            $this->assertStringStartsWith('Expected "-", "0"', $e->getMessage());
        }
        try {
            TestRecord::pmql('foo IN "foo"');
        } catch (SyntaxError $e) {
            $this->assertStringStartsWith('Expected "["', $e->getMessage());
        }
    }
}
