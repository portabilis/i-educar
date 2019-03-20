<?php

use App\Models\Institution;
use App\Models\RegimeType;
use Faker\Generator as Faker;

$factory->define(RegimeType::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_tipo' => $faker->words(3, true),
        'data_cadastro' => now(),
        'ativo' => 1,
        'ref_cod_instituicao' => factory(Institution::class)->states('unique')->make(),
    ];
});
