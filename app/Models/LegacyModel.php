<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegacyModel extends Model
{
    public function __get($key)
    {
        if (array_key_exists($key, $this->legacy)) {
            return parent::__get($this->legacy[$key]);
        }

        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->legacy)) {
            parent::__set($this->legacy[$key], $value);
        }

        parent::__set($key, $value);
    }

    public function newEloquentBuilder($query)
    {
        if (property_exists($this, 'builder')) {
            return new $this->builder($query);
        }

        return new Builder($query);
    }

    public function attributesToArray()
    {
        if (property_exists($this, 'legacy')) {
            $legacy = array_flip($this->legacy);
            $new_attributes = [];
            foreach (parent::attributesToArray() as $key => $value) {
                $new_attributes[$legacy[$key] ?? $key] = $value;
            }

            return $new_attributes;
        }

        return parent::attributesToArray();
    }

    public function fill(array $attributes)
    {
        if (property_exists($this, 'legacy')) {
            foreach ($attributes as $key => $value) {
                $this->setAttribute($this->legacy[$key] ?? $key, $value);
            }

            return $this;
        }

        return parent::fill($attributes);
    }
}
