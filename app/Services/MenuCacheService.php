<?php

namespace App\Services;

use App\Menu;
use App\User;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MenuCacheService
{
    private const TTL_MENU_TREE = 86400;

    private const TTL_PROCESS_MENU = 604800;

    private ConfigContract $config;

    private CacheContract $cache;

    public function __construct(ConfigContract $config, CacheContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @return array|mixed|null
     */
    public function getMenuByUser(User $user)
    {
        $key = $this->getUserKey($user);
        $client = $this->getClient();

        $cacheMenus = $this->cache->tags(['menus', $client, $key])->get($key);

        if ($cacheMenus !== null) {
            return $cacheMenus;
        }

        $menus = Menu::user($user);
        $this->putMenuCache($menus, $user);

        return $menus;
    }

    public function putMenuCache(Collection $adminMenus, User $user)
    {
        $key = $this->getUserKey($user);
        $client = $this->getClient();

        $this->cache->tags(['menus', $client, $key])->put($key, $adminMenus, self::TTL_MENU_TREE);
    }

    /**
     * @return array{attributes: array, root_id: int, ancestor_ids: array<int>}|false
     */
    public function getProcessMenuData(int|string|null $process): array|false
    {
        if (empty($process)) {
            return false;
        }

        $client = $this->getClient();

        return $this->cache->tags(['menus', $client])->remember(
            'menu-process-' . $client . '-' . $process,
            self::TTL_PROCESS_MENU,
            function () use ($process) {
                $menu = Menu::query()->where('process', $process)->first();

                if ($menu === null) {
                    return false;
                }

                $ancestors = $menu->ancestors()->get();

                return [
                    'attributes' => $menu->getAttributes(),
                    'root_id' => $ancestors->whereNull('parent_id')->first()?->getKey() ?? $menu->getKey(),
                    'ancestor_ids' => $ancestors->pluck('id')->all(),
                ];
            }
        );
    }

    public function flushAll(): void
    {
        $this->cache->tags(DB::connection()->getConfig('database'))->flush();
    }

    public function flushMenuTag($tagMenu)
    {
        $this->cache->tags('menu-' . $this->getClient() . '-' . $tagMenu)->flush();
    }

    private function getClient(): string
    {
        return $this->config->get('legacy.app.database.dbname');
    }

    private function getUserKey(User $user): string
    {
        return 'menu-' . $this->getClient() . '-' . $user->type->cod_tipo_usuario;
    }
}
