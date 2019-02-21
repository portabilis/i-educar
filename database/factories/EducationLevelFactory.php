<?php

use App\Models\EducationLevel;
use App\Models\Institution;
use Faker\Generator as Faker;

$factory->define(EducationLevel::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_nivel' => $faker->word,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(Institution::class)->states('unique')->make(),
    ];
});
