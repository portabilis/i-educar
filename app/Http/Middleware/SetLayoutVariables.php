<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class SetLayoutVariables
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
        View::share('loggedUser', session('logged_user'));
        View::share('config', config('legacy'));

        return $next($request);
    }
}
