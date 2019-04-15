<?php
namespace ProcessMaker\Query;

class JsonField extends BaseField
{

    public function toArray()
    {
        return [
            'JsonField' => $this->field
        ];
    }

    public function toEloquent()
    {
        return str_replace('.', '->', $this->field);
    }

}