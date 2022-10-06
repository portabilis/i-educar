<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use Database\Factories\CityFactory;
use Tests\EloquentTestCase;

class CityTest extends EloquentTestCase
{
    protected $relations = [
        'districts' => [District::class],
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return City::class;
    }
}
