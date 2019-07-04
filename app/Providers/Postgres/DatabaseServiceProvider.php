<?php

namespace App\Providers\Postgres;

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
        $app = $this->app;
        $this->app['db']->extend('pgsql', function ($config, $name) use($app) {
            $connection = $app['db.factory']->make($config, $name);

            $new_connection = new PostgresConnection(
                $connection->getPdo(),
                $connection->getDatabaseName(),
                $connection->getTablePrefix(),
                $config
            );

            return $new_connection;
        });
    }
}