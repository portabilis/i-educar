<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class Footer
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        View::share('footer', config('legacy.config.ieducar_internal_footer'));

        return $next($request);
    }
}
