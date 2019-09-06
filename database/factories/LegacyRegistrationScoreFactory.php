<?php

use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationScore;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyRegistrationScore::class, function (Faker $faker) {
    return [
        'matricula_id' => factory(LegacyRegistration::class)->create(),
    ];
});
