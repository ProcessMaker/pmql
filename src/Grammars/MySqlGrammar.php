<?php

namespace ProcessMaker\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseMySqlGrammar;

class MySqlGrammar extends BaseMySqlGrammar
{
    /**
     * A hack to bubble up the wrapJsonSelector functionality to a public interface
     */
    public function wrapJsonSelector($value)
    {
        $parts = explode('->', $value);

        if (count($parts) === 2) {
            // Case simple: "data->interest_check"
            [$field, $path] = $parts;
        } else {
            // Case complex: "collection_3->data->interest_check"
            // Take all except the last element as field
            $path = array_pop($parts);
            $field = implode('.', $parts);
        }

        return 'LEFT(' . $field . '->>"$.' . str_replace('->', '.', $path) . '", 255)';
    }
}
