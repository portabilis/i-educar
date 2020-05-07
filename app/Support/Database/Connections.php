<?php

namespace App\Support\Database;


trait Connections
{
    public function getConnections()
    {
        $connections = config('database.connections');

        return array_diff(array_keys($connections), ['sqlite', 'mysql', 'pgsql', 'sqlsrv', 'bussolastaging']);
    }
}
