<?php

use App\Models\Individual;
use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Student::class, function (Faker $faker) {
    return [
        'ref_idpes' => factory(Individual::class)->create(),
        'data_cadastro' => now(),
    ];
});
