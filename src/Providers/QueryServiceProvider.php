<?php
namespace ProcessMaker\Query\Providers;

use Illuminate\Support\ServiceProvider;
use ProcessMaker\Query\Facades\QueryManager;
use ProcessMaker\Query\Query;

class QueryServiceProvider extends ServiceProvider
{

    public $singletons = [
        QueryManager::class => QueryManager::class
    ];

    public function boot()
    {
        // Empty
    }

    public function register()
    {
        $this->app->bind(Query::class, function($app) {
            // Return a new query instance with our query manager singleton
            return new Query($this->app->make(QueryManager::class));
        });
    }
}