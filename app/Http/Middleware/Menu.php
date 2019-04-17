<?php

namespace App\Http\Middleware;

use App\Models\LegacyUser;
use App\Services\MenuService;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class Menu
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
        View::share('menu', $this->getCachedMenu());

        return $next($request);
    }

    private function getCachedMenu()
    {
        $cache = Cache::tags(['menu', config('app.nickname')]);

        $cacheKey =  'menu_' . md5(session('id_pessoa'));

        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        if (empty(LegacyUser::find(session('id_pessoa')))) {
            return [];
        }

        /** @var MenuService $menuService */
        $menuService = app(MenuService::class);
        $menuArray = $menuService->getByUser(LegacyUser::find(session('id_pessoa')));

        $cache->add($cacheKey, $menuArray, 60);

        return $menuArray;
    }
}
