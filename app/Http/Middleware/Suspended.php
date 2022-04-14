<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
        $active = Config::get('legacy.config.active_on_ieducar');
        $level = Session::get('nivel');

        if ($active || $level === 1) {
            return $next($request);
        }

        return new RedirectResponse('/intranet/suspenso.php');
    }
}
