<?php

namespace App\Http\Middleware;

use App\Entities\User;
use App\Services\MenuService;
use Closure;
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
        /** @var MenuService $menuService */
        $menuService = app(MenuService::class);
        $menuArray = $menuService->getByUser(User::find(session('id_pessoa')));

        View::share('menu', $menuArray);

        return $next($request);
    }
}
