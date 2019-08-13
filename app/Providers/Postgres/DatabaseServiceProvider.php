<?php

namespace App\Providers\Postgres;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use PDO;

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

            $new_connection->getPdo()->exec('SET search_path = "$user", public, portal, cadastro, pmieducar, urbano, modules;');

            return $new_connection;
        });

        Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
            $query->connection->setFetchMode(PDO::FETCH_OBJ);
        });
    }
}
