<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Support\Repositories\ConfigurationRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class Footer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        View::share('footer', $this->getCachedFooter());

        return $next($request);
    }

    private function getCachedFooter()
    {
        $cache = Cache::tags(['configurations', config('app.name')]);

        $cacheKey = 'configurations_' . md5(session('id_pessoa'));
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey)->ieducar_internal_footer;
        }

        $configurationRepository = app(ConfigurationRepository::class);
        $configurations = $configurationRepository->getConfiguration();

        $cache->add($cacheKey, $configurations, 60);

        return $configurations->ieducar_internal_footer;
    }
}
