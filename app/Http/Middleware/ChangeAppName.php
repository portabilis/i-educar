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
        config([
            'app.name' => config('legacy.config.ieducar_entity_name'),
            'app.nickname' => url('/'),
            'app.url' => $request->getBasePath(),
            'honeybadger.environment_name' => $request->getHost(),
        ]);

        return $next($request);
    }
}
