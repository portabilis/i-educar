<?php

use App\Models\LegacyPerson;
use App\PersonHasPlace;
use App\Models\Place;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(PersonHasPlace::class, function (Faker $faker) {
    return [
        'person_id' => factory(LegacyPerson::class)->create(),
        'place_id' => factory(Place::class)->create(),
        'type' => 1,
    ];
});
