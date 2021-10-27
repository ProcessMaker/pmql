<?php

namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\Tests\TestCase;

class Peg_f18Test extends TestCase
{
    /**
     * Tests a returning a float when passing a string float value
     */
    public function testStringFloatValueShouldReturnAFloatLiteralValue()
    {
        $parser = new Parser();
        $val = "80.0";
        $parsed = $parser->parse('value < ' . $val);

        $this->assertEquals(
            gettype($parsed->toArray()['expressions'][0]['value']['LiteralValue']),
            "double"
        );
    }

     /**
     * Tests a returning a integer when passing a string integer value
     */
    public function testStringIntValueShouldReturnAIntLiteralValue()
    {
        $parser = new Parser();
        $val = "80";
        $parsed = $parser->parse('value < ' . $val);

        $this->assertEquals(
            gettype($parsed->toArray()['expressions'][0]['value']['LiteralValue']),
            "integer"
        );
    }
}
