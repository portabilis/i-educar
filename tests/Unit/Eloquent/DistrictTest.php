<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use Tests\EloquentTestCase;

class DistrictTest extends EloquentTestCase
{
    /**
     * @var string[]
     */
    protected $relations = [
        'city' => City::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return District::class;
    }
}
