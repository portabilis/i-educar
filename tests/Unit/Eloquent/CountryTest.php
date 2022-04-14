<?php

namespace Tests\Unit\Eloquent;

use App\Models\Country;
use Tests\EloquentTestCase;

class CountryTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Country::class;
    }
}
