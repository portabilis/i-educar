<?php

namespace App\Services;

use App\User;
use Illuminate\Cache\CacheManager as LaravelCacheManager;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CacheManager //extends LaravelCacheManager
{
    /**
     * Invalida todas as entradas de cache de acordo com as tags passadas
     *
     * @param $tags
     *
     * @return Repository
     */
    public static function invalidateByTags($tags)
    {
        if (self::supportsTags(Cache::store()->getStore())) {
            Cache::tags($tags)->flush();
        }

        return Cache::store();
    }

    /**
     * Sobreescreve o método que chama todas as ações de cache para setar o prefixo, que será definido de
     * acordo com o tenant
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method == 'tags' && !self::supportsTags($this->store()->getStore())) {
            return $this->store();
        }

        if (self::supportsPrefix($this->store()->getStore())) {
            $this->store()->setPrefix(config('app.nickname'));
        }

        return $this->store()->$method(...$parameters);
    }

    /**
     * Checks if cache driver supports tags use
     *
     * @param $store
     *
     * @return bool
     */
    private static function supportsTags($store)
    {
        $doNotSupportTags = [
            'Illuminate\Cache\DatabaseStore',
            'Illuminate\Cache\FileStore',
        ];

        if (in_array(get_class($store), $doNotSupportTags)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if cache driver supports prefix use
     *
     * @param $store
     *
     * @return bool
     */
    private static function supportsPrefix($store)
    {
        $supportPrefix = [
            'Illuminate\Cache\RedisStore',
        ];

        if (in_array(get_class($store), $supportPrefix)) {
            return true;
        }

        return false;
    }

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
}
