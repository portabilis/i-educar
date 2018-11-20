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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        View::share('footer', $this->getCachedFooter());

        return $next($request);
    }

    private function getCachedFooter()
    {
        // TODO: Criar classe de cache manager baseada na PSR-6
        $cacheKey =  'configurations_' . md5(request()->getHttpHost() . session('id_pessoa'));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey)->ieducar_internal_footer;
        }

        $configurationRepository = app(ConfigurationRepository::class);
        $configurations = $configurationRepository->getConfiguration();

        Cache::add($cacheKey, $configurations, 60);

        return $configurations->ieducar_internal_footer;
    }
}
