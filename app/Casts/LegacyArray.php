<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LegacyArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return transformStringFromDBInArray($value) ?? [];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return transformDBArrayInString($value);
    }
}
