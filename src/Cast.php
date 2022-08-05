<?php

namespace ProcessMaker\Query;

use Illuminate\Support\Facades\DB;
use ProcessMaker\Query\SyntaxError;

class Cast extends BaseField
{
    protected $field;

    protected $type;

    protected $types = [
        'number' => [
            'mysql' => 'decimal',
            'sqlite' => 'integer',
        ],
        'text' => [
            'mysql' => 'char',
            'sqlite' => 'text',
        ],
    ];

    public function __construct($field, $type)
    {
        $this->field = $field;
        $this->type = $this->mapType($type);
    }

    public function toArray()
    {
        return [
            'Cast' => [
                'field' => $this->field->toArray(),
                'type' => $this->type,
            ],
        ];
    }

    private function getConnectionDriver()
    {
        $config = DB::connection()->getConfig();

        return $config['driver'];
    }

    private function getSupportedTypes()
    {
        $list = [];

        foreach ($this->types as $key => $value) {
            $list[] = $key;
        }

        return $list;
    }

    private function mapType($type)
    {
        $driver = $this->getConnectionDriver();

        if (array_key_exists($type, $this->types)) {
            if (array_key_exists($driver, $this->types[$type])) {
                return $this->types[$type][$driver];
            }
        }

        $types = implode(', ', $this->getSupportedTypes());
        throw new SyntaxError("Unsupported cast type. Casts must be of type: {$types}.", $this->getSupportedTypes(), $type, 0, 0, 0);
    }

    public function toEloquent($connection = null)
    {
        return DB::raw('CAST('.$this->field->toEloquent($connection).' AS '.$this->type.')');
    }
}
