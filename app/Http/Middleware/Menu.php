<?php

namespace App\Http\Middleware;

use App\Entities\User;
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
        // TODO: Criar classe de cache manager baseada na PSR-6
        $cacheKey =  md5(request()->getHttpHost() . session('id_pessoa'));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        /** @var MenuService $menuService */
        $menuService = app(MenuService::class);
        $menuArray = $menuService->getByUser(User::find(session('id_pessoa')));

        Cache::add($cacheKey, $menuArray, 1);

        return $menuArray;
    }
}
