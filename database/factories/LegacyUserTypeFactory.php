<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyUserType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyUserType::class, function (Faker $faker) {
    return [
        'nm_tipo' => $faker->firstName,
        'nivel' => $faker->randomElement([1, 2, 3, 4]),
        'ref_funcionario_cad' => function () {
            return factory(LegacyEmployee::class)->create()->ref_cod_pessoa_fj;
        },
        'data_cadastro' => $faker->dateTime,
    ];
});
