<?php

use App\Models\LegacySchool;
use App\Models\SchoolInep;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(SchoolInep::class, function (Faker $faker) {
    return [
        'cod_escola' => factory(LegacySchool::class)->create(),
        'cod_escola_inep' => $faker->numerify('########'),
    ];
});
