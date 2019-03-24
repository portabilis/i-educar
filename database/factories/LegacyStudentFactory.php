<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;
use Faker\Generator as Faker;

$factory->define(LegacyStudent::class, function (Faker $faker) {
    return [
        'ref_idpes' => factory(LegacyIndividual::class)->create(),
        'data_cadastro' => now(),
    ];
});
