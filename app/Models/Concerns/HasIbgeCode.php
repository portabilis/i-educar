<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasIbgeCode
{
    /**
     * @param int $ibgeCode
     * @return Model|null
     */
    public static function findByIbgeCode($ibgeCode)
    {
        return static::query()->where('ibge_code', $ibgeCode)->first();
    }
}
