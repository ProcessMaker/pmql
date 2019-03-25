<?php
namespace ProcessMaker\Query\Facades;

use Illuminate\Support\Facades\Facade;

class QueryManager extends Facade
{
    protected static function getFacadeAccessor() { return '\\ProcessMaker\\Query\\QueryManager'; }
}