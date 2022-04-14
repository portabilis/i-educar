<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class ChangeAppName
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config([
            'app.name' => config('legacy.config.ieducar_entity_name'),
            'app.nickname' => url('/'),
            'app.slug' => DB::getDefaultConnection(),
            'app.url' => $request->getBasePath(),
            'honeybadger.environment_name' => config('app.env'),
        ]);

        return $next($request);
    }
}
