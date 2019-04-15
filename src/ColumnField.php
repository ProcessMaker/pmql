<?php
namespace ProcessMaker\Query;

class ColumnField extends BaseField
{
    public function toArray()
    {
        return [
            'ColumnField' => $this->field
        ];
    }

}