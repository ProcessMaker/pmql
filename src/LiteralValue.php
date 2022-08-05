<?php

namespace ProcessMaker\Query;

class LiteralValue extends BaseValue
{
    public function toArray()
    {
        return [
            'LiteralValue' => $this->value(),
        ];
    }
}
