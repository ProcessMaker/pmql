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
        [$field, $path] = explode('->', $value, 2);

        return 'LEFT(' . $field . '->>"$.' . str_replace('->', '.', $path) . '", 255)';
    }
}
