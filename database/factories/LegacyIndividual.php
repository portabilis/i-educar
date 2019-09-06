<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyIndividual::class, function (Faker $faker) {
    return [
        'idpes' => function() {
            return factory(LegacyPerson::class)->create()->idpes;
        },
        'origem_gravacao' => 'U',
        'data_cad' => $faker->dateTime,
        'operacao' => 'I',
    ];
});
