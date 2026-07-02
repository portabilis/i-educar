<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait Connections
{
    public function getConnections()
    {
        $connections = config('database.connections');

        return array_diff(array_keys($connections), ['sqlite', 'mysql', 'pgsql', 'sqlsrv', 'bussolastaging', 'mariadb', 'audit']);
    }

    public function eachConnection(callable $callback): void
    {
        foreach ($this->getConnections() as $connection) {
            try {
                DB::setDefaultConnection($connection);
                $callback($connection);
            } finally {
                DB::purge($connection);
            }
        }
    }
}
