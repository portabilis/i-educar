<?php

namespace App\Http\Middleware;

use Closure;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LoadSettings
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
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        Config::set($settings);

        return $next($request);
    }
}
