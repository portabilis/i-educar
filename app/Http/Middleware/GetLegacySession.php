<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Modules\Auth\GetSessionData;

class GetLegacySession
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
        session(GetSessionData::get());
        return $next($request);
    }
}
