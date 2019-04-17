<?php
namespace ProcessMaker\Query\Tests\Feature;

use Illuminate\Database\MySqlConnection;
use ProcessMaker\Query\JsonField;
use ProcessMaker\Query\SyntaxError;
use ProcessMaker\Query\Tests\TestCase;

class GrammarTest extends TestCase
{
    public function testSqliteJson()
    {
        $field = new JsonField("data.customer.name");
        $result = $field->toEloquent();
        $this->assertEquals('json_extract("data", \'$."customer"."name"\')', $result);
    }

    public function testMysqlJson()
    {
        $field = new JsonField("data.customer.name");
        // Create a mysql connection, doesn't have to work
        $connection = new MySqlConnection(function() { }, "test", "");
        $result = $field->toEloquent($connection);
        $this->assertEquals('`data`->\'$."customer"."name"\'', $result);
    }
}
