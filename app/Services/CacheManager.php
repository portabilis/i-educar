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
     */
    public static function invalidateByTags($tags)
    {
        Cache::tags($tags)->flush();
    }

    /**
     * Sobreescreve o método que chama todas as ações de cache para setar o prefixo, que será definido de
     * acordo com o tenant
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->store()->setPrefix(config('app.name'));
        return $this->store()->$method(...$parameters);
    }
}
