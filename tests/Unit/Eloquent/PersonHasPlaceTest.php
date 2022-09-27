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
        $this->markTestSkipped();
        return PersonHasPlace::class;
    }
}
