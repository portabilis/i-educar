<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssByPass
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request)
            ->header('X-XSS-Protection', 0);

        return $response;
    }
}
