<?php

namespace ProcessMaker\Query;

use Exception;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Support\Facades\DB;

class JsonField extends BaseField
{
    public function toArray()
    {
        return [
            'JsonField' => $this->field,
        ];
    }

    public function toEloquent($connection = null)
    {
        if (!$connection) {
            $connection = DB::connection();
        }
        $grammar = $connection->getQueryGrammar();
        // Convert to Laravel Database Json Syntax
        $value = str_replace('.', '->', $this->field);
        if (is_a($grammar, MySqlGrammar::class)) {
            return $connection->raw((new \ProcessMaker\Query\Grammars\MySqlGrammar)->wrapJsonSelector($value));
        } elseif (is_a($grammar, SQLiteGrammar::class)) {
            return $connection->raw((new \ProcessMaker\Query\Grammars\SQLiteGrammar)->wrapJsonSelector($value));
        } else {
            throw new Exception('Unsupported query grammar for handling JSON fields.');
        }
    }
}
