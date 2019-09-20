<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyRegimeType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyRegimeType::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_tipo' => $faker->words(3, true),
        'data_cadastro' => now(),
        'ativo' => 1,
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->states('unique')->make(),
    ];
});
