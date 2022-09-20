<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\LegacyBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegacyModel extends Model
{
    public array $legacy = [];

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

        return new LegacyBuilder($query);
    }

    public function attributesToArray()
    {
        if (property_exists($this, 'legacy')) {
            $legacy = array_flip($this->legacy);
            $newAttributes = [];
            foreach (parent::attributesToArray() as $key => $value) {
                $newAttributes[$legacy[$key] ?? $key] = $value;
            }

            return $newAttributes;
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

    public function getLegacyColumn($column)
    {
        return $this->legacy[$column] ?? $column;
    }
}
