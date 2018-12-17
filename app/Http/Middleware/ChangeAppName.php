<?php

namespace App\Http\Middleware;

use Closure;

class ChangeAppName
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
        config(['app.name' => request()->getHost()]);
        config(['honeybadger.environment_name' => request()->getHost()]);

        return $next($request);
    }
}
