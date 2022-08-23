<?php

namespace ProcessMaker\Query\Tests\Feature;

use ProcessMaker\Query\Parser;
use ProcessMaker\Query\Tests\TestCase;

class NumberCastingTest extends TestCase
{
    /**
     * Tests a returning a float when passing a string float value
     */
    public function testStringFloatValueShouldReturnAFloatLiteralValue()
    {
        $parser = new Parser();
        $val = '80.0';
        $parsed = $parser->parse('value < ' . $val);

        $this->assertEquals(
            gettype($parsed->toArray()['expressions'][0]['value']['LiteralValue']),
            'double'
        );
    }

    /**
     * Tests a returning a integer when passing a string integer value
     */
    public function testStringIntValueShouldReturnAIntLiteralValue()
    {
        $parser = new Parser();
        $val = '80';
        $parsed = $parser->parse('value < ' . $val);

        $this->assertEquals(
            gettype($parsed->toArray()['expressions'][0]['value']['LiteralValue']),
            'integer'
        );
    }

    /**
     * Tests returning an empty string when an array with an empty value passed
     */
    public function testArrayWithEmptyValueShouldReturnAnEmptyString()
    {
        $str = '';
        // Flatstr will receive the value "" as an array with an empty value
        $arr = str_split($str);
        $flatted = \ProcessMaker\Query\Processor::flatstr(
            \ProcessMaker\Query\Processor::flatten($arr, true), true
        );

        $this->assertEquals($flatted, $str);
    }

    /**
     * Tests returning an empty string when an empty array passed
     */
    public function testEmptyArrayShouldReturnAnEmptyString()
    {
        $arr = [];
        $flatted = \ProcessMaker\Query\Processor::flatstr(
            \ProcessMaker\Query\Processor::flatten($arr, true), true
        );

        $this->assertEquals($flatted, '');
    }

    /**
     * Tests returning an empty string when an null array passed
     */
    public function testNullArrayShouldReturnAnEmptyString()
    {
        $arr = null;
        $flatted = \ProcessMaker\Query\Processor::flatstr(
            \ProcessMaker\Query\Processor::flatten($arr, true), true
        );

        $this->assertEquals($flatted, '');
    }

    /**
     * Tests returning a flatted string when passing a string with chars values
     */
    public function testArrayOfCharsShouldReturnAnStringValue()
    {
        $str = '80.0';
        // Parser will receive the value 80.0 as an array of chars ..
        $arr = str_split($str);
        $flatted = \ProcessMaker\Query\Processor::flatstr(
            \ProcessMaker\Query\Processor::flatten($arr, true), true
        );

        $this->assertEquals($flatted, $str);
    }
}
