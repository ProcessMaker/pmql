<?php
namespace ProcessMaker\Query\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    // Be sure to start our query service provider
    protected function getPackageProviders($app) 
    {
        return ['ProcessMaker\Query\Providers\QueryServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }
}