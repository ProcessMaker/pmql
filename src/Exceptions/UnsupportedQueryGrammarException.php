<?php

namespace ProcessMaker\Query\Exceptions;

use Exception;

class UnsupportedQueryGrammarException extends Exception
{
    public function __construct(
        $message = 'Unsupported query grammar for handling JSON fields.',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
