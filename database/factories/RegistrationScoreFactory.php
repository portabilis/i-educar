<?php

use App\Models\Registration;
use App\Models\RegistrationScore;
use Faker\Generator as Faker;

$factory->define(RegistrationScore::class, function (Faker $faker) {
    return [
        'matricula_id' => factory(Registration::class)->create(),
    ];
});
