<?php

namespace App\Services;

use App\Menu;
use App\User;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Database\Eloquent\Collection;

class MenuCacheService
{
    private ConfigContract $config;
    private CacheContract $cache;

    public function __construct(ConfigContract $config, CacheContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @param User $user
     *
     * @return array|mixed|null
     */
    public function getMenuByUser(User $user)
    {
        $key = $this->getUserKey($user);
        $client = $this->config->get('legacy.app.database.dbname');

        $cacheMenus = $this->cache->tags(['menus', $client, $key])->get($key);

        if ($cacheMenus !== null) {
            return $cacheMenus;
        }

        $menus = Menu::user($user);
        $this->putMenuCache($menus, $user);

        return $menus;
    }

    /**
     * @param Collection $adminMenus
     * @param User       $user
     */
    public function putMenuCache(Collection $adminMenus, User $user)
    {
        $key = $this->getUserKey($user);
        $client = $this->config->get('legacy.app.database.dbname');

        $this->cache->tags(['menus', $client, $key])->put($key, $adminMenus, env('CACHE_TTL', 60));
    }

    /**
     * @param $tagMenu
     */
    public function flushMenuTag($tagMenu)
    {
        $this->cache->tags('menu-' . $this->config->get('legacy.app.database.dbname') . '-' . $tagMenu)->flush();
    }

    private function getUserKey(User $user): string
    {
        return 'menu-' . $this->config->get('legacy.app.database.dbname') . '-' . $user->type->cod_tipo_usuario;
    }
}
