<?php
namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Query;
use ProcessMaker\Query\SyntaxError;
use Orchestra\Testbench\TestCase;


class GrammarTest extends TestCase
{
    /**
     * Test to see if our parser supports the simple arithmetic of 1+1
     */
    public function testSimpleGrammar()
    {
        $query = app()->make(Query::class);
        $this->assertEquals($query->parse('1+1'), 2);
    }

    /**
     * Ensure we throw a grammar error. 
     */
    public function testGrammarError()
    {
        $this->expectException(SyntaxError::class);
        $query = app()->make(Query::class);
        dd($query->parse('invalid'));
   }
}