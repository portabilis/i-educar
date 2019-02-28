<?php

use App\Models\EducationType;
use App\Models\Institution;
use Faker\Generator as Faker;

$factory->define(EducationType::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_tipo' => $faker->word,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(Institution::class)->states('unique')->make(),
    ];
});
