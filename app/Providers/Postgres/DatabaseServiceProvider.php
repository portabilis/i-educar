<?php

namespace App\Providers\Postgres;

use Illuminate\Database\DatabaseServiceProvider as ParentDatabaseServiceProvider;
use Illuminate\Database\Connectors\PostgresConnector;

class DatabaseServiceProvider extends ParentDatabaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        $factory = $this->app['db'];
        $factory->extend('pgsql', function($config) {
            $connector =  new PostgresConnector();
            $pdo = $connector->connect($config);
            return new PostgresConnection($pdo, $config['database'], $config['prefix']);
        });
    }
}
