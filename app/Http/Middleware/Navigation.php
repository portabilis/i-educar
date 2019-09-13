<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Navigation
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
        View::share('breadcrumb', app(Breadcrumb::class));

        return $next($request);
    }
}
