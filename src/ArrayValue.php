<?php

namespace ProcessMaker\Query;

class ArrayValue extends BaseValue
{
    public function value()
    {
        if (is_array($this->value) && $this->value[0] === '[') {
            $list = $this->value[2];
            if (is_array($list[0])) {
                $result = $this->extractValue($list[0]);
            } else {
                $result = $list;
            }

            return $result;
        } else {
            return $this->value;
        }
    }

    private function extractValue($chunk, &$values = [])
    {
        $values[] = (string) $chunk[0];
        if (is_array($chunk[3])) {
            $this->extractValue($chunk[3], $values);
        } else {
            $values[] = (string) $chunk[3];
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
