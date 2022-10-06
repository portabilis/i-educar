<?php

namespace Tests\Unit\Eloquent;

use App\Models\PersonHasPlace;
use Tests\EloquentTestCase;

class PersonHasPlaceTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return PersonHasPlace::class;
    }
}
