<?php

namespace App\Providers\Postgres;

use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['db']->extend('pgsql', function($config) {
            $connector =  new PostgresConnector();
            $pdo = $connector->connect($config);
            return new PostgresConnection($pdo, $config['database'], $config['prefix']);
        });
    }
}
