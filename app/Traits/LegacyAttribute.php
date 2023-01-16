<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait LegacyAttribute
{
    public function newEloquentBuilder($query)
    {
        if (property_exists($this, 'builder')) {
            return new $this->builder($query);
        }

        return new Builder($query);
    }
}
