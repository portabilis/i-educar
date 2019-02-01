<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConnectTenantDatabase
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
        $connections = config('database.connections');
        $tenant = $request->getSubdomain();

        if (isset($connections[$tenant])) {
            DB::setDefaultConnection($tenant);
        }

        return $next($request);
    }
}
