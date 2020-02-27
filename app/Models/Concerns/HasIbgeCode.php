<?php

namespace App\Models\Concerns;

trait HasIbgeCode
{
    /**
     * @param int $ibgeCode
     *
     * @return $this
     */
    public static function findByIbgeCode($ibgeCode)
    {
        return static::query()->where('ibge_code', $ibgeCode)->first();
    }
}
