<?php

namespace App\Http\Middleware;

use Closure;

class Navigation
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
        $breadCrumb = \Route::current()->controller->getBreadCrumb();
        \View::share('breadCrumb', $breadCrumb);

        return $next($request);
    }
}
