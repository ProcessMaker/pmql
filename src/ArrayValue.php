<?php

namespace ProcessMaker\Query;

class ArrayValue extends BaseValue
{
    public function value()
    {
        $list = $this->value[2];
        if (is_array($list[0])) {
            $result = $this->extractValue($list[0]);
        } else {
            $result = $list;
        }

        return $result;
    }

    private function extractValue($chunk, &$values = [])
    {
        $values[] = $chunk[0];
        if (is_array($chunk[3])) {
            $this->extractValue($chunk[3], $values);
        } else {
            $values[] = $chunk[3];
        }

        return $values;
    }

    public function toArray()
    {
        return [
            'ArrayValue' => $this->value(),
        ];
    }
}
