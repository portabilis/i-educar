<?php

namespace App\Services;

use Illuminate\Cache\CacheManager as LaravelCacheManager;
use Illuminate\Support\Facades\Cache;

class CacheManager extends LaravelCacheManager
{

    /**
     * Invalida todas as entradas de cache de acordo com as tags passadas
     *
     * @param $tags
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public static function invalidateByTags($tags)
    {
        if (!self::supportsTags()) {
            return Cache::store();
        }

        Cache::tags($tags)->flush();
    }

    /**
     * Sobreescreve o método que chama todas as ações de cache para setar o prefixo, que será definido de
     * acordo com o tenant
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method == 'tags' && !self::supportsTags()) {
            return $this->store();
        }

        if (self::supportsPrefix()) {
            $this->store()->setPrefix(config('app.name'));
        }

        return $this->store()->$method(...$parameters);
    }

    private static function supportsTags()
    {
        $doNotSupportTags = [
            'file', 'database'
        ];

        if (in_array(config('cache.default'), $doNotSupportTags)) {
            return false;
        }

        return true;
    }

    private static function supportsPrefix()
    {
        $supportPrefix = [
            'redis'
        ];

        if (in_array(config('cache.default'), $supportPrefix)) {
            return true;
        }

        return false;
    }
}
