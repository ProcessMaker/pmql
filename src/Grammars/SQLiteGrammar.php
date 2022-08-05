<?php

namespace ProcessMaker\Query\Grammars;

use Illuminate\Database\Query\Grammars\SQLiteGrammar as BaseSQLiteGrammar;

class SQLiteGrammar extends BaseSQLiteGrammar
{
    /**
     * A hack to bubble up the wrapJsonSelector functionality to a public interface
     */
    public function wrapJsonSelector($value)
    {
        return parent::wrapJsonSelector($value);
    }
}
