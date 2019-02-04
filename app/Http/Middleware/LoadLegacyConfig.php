<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Support\Config\LegacyConfig;

class LoadLegacyConfig
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $legacyConfigPath = base_path() . '/' . config('legacy.path') . '/configuration/';

        $configObject = new LegacyConfig($legacyConfigPath, config('legacy.env'), request()->getHttpHost());

        $laravelLegacyConfig = config()->get('legacy');

        config()->set(['legacy' => array_merge($laravelLegacyConfig, $configObject->getArrayConfig())]);

        return $next($request);
    }
}
