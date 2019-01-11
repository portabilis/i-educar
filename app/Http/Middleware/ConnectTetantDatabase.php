<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConnectTetantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $envDatabase = config('database.connections.pgsql.database');

        $legacyConfigDatabase = config('legacy.app.database.dbname');

        if ($envDatabase == $legacyConfigDatabase) {
            return $next($request);
        }

        DB::purge('pgsql');
        config('database.connections.pgsql.database', $legacyConfigDatabase);
        DB::reconnect('pgsql');
        Schema::connection('pgsql')->getConnection()->reconnect();

        return $next($request);
    }
}
