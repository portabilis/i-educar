<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token && config('legacy.apis.access_key') === $token) {
            return $next($request);
        }

        throw new UnauthorizedException();
    }
}
