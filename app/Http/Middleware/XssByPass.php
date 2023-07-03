<?php

namespace App\Http\Middleware;

use Closure;

class XssByPass
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request)
            ->header('X-XSS-Protection', 0);

        return $response;
    }
}
