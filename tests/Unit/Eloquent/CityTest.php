<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use Tests\EloquentTestCase;

class CityTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return City::class;
    }
}
