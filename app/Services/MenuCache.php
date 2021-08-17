<?php

namespace App\Services;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class MenuCache
{
    /**
     * @param User $user
     * @return array|mixed|null
     */
    public function getMenuByUser(User $user)
    {
        $key = $user->getMenuCacheKey();
        $client = config('legacy.app.database.dbname');

        return Cache::tags(['menus', $client, $key])->get($key);
    }

    /**
     * @param Collection $adminMenus
     * @param User $user
     */
    public function putMenuCache(Collection $adminMenus, User $user)
    {
        $key = $user->getMenuCacheKey();
        $client = config('legacy.app.database.dbname');

        Cache::tags(['menus', $client, $key])->put($key, $adminMenus, env('CACHE_TTL',60));
    }

    /**
     * @param $tagMenu
     */
    public function flushMenuTag($tagMenu)
    {
        Cache::tags('menu-' . config('legacy.app.database.dbname') . '-' . $tagMenu)->flush();
    }
}
