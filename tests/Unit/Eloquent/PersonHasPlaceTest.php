<?php

namespace Tests\Unit\Eloquent;

use App\Models\Person;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Tests\EloquentTestCase;

class PersonHasPlaceTest extends EloquentTestCase
{
    protected $relations = [
        'place' => Place::class,
        'person' => Person::class,
    ];

    protected function getEloquentModelName(): string
    {
        return PersonHasPlace::class;
    }
}
