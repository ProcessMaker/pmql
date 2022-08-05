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
        return parent::wrapJsonSelector($value);
    }
}
