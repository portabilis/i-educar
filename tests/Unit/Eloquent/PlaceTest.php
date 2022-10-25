<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\Place;
use Tests\EloquentTestCase;

class PlaceTest extends EloquentTestCase
{
    protected $relations = [
        'city' => City::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return Place::class;
    }
}
