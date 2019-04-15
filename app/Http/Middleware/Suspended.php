<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Suspended
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
        $active = config('legacy.config.active_on_ieducar');

        if ($active) {
            return $next($request);
        }

        return new RedirectResponse('/intranet/suspenso.php');
    }
}
