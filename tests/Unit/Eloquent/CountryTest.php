<?php

namespace Tests\Unit\Eloquent;

use App\Models\Country;
use App\Models\State;
use Database\Factories\CountryFactory;
use Tests\EloquentTestCase;

class CountryTest extends EloquentTestCase
{
    protected $relations = [
        'states' => [State::class]
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Country::class;
    }
}
