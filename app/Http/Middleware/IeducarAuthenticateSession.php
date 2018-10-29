<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Modules\Auth\VerifyAuthenticateSession;

class IeducarAuthenticateSession
{
    const LOGIN_ROUTE = '/intranet/index.php';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!VerifyAuthenticateSession::isAuthenticated()) {
            return redirect(self::LOGIN_ROUTE);
        }

        return $next($request);
    }
}
