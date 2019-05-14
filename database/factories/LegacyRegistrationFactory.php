<?php

use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyRegistration::class, function (Faker $faker) {
    return [
        'ref_cod_aluno' => factory(LegacyStudent::class)->create(),
        'data_cadastro' => now(),
        'ano' => now()->year,
        'ref_usuario_cad' => 1,
    ];
});
