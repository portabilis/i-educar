<?php

use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyEducationType::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_tipo' => $faker->word,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->states('unique')->make(),
    ];
});
