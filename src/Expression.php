<?php

namespace ProcessMaker\Query;

class Expression extends BaseExpression
{
    const ARRAY_OPERATORS = [
        'IN',
        'NOT IN',
    ];

    protected $field;

    protected $operator;

    protected $value;

    public function __construct(BaseField $field, $operator, $value, $logical = 'AND')
    {
        parent::__construct($logical);
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * Magic getter for our fields.
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * Dumps information to array. This can be used for terse testing
     */
    public function toArray()
    {
        return [
            'field' => $this->field->toArray(),
            'operator' => $this->operator,
            'value' => $this->value->toArray(),
            'logical' => $this->logical,
        ];
    }

    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
}
