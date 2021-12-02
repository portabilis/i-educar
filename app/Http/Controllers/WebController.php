<?php

namespace App\Http\Controllers;

use App\Services\MenuCacheService;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function user(Request $request)
    {
        return $request->user();
    }

    public function menus(MenuCacheService $menus, Request $request)
    {
        return $menus->getMenuByUser($request->user());
    }
}
