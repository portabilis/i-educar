<?php

namespace Tests\Unit\Eloquent;

use App\Models\Place;
use Tests\EloquentTestCase;

class PlaceTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Place::class;
    }
}
