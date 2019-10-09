<?php

use App\Models\LegacyExemptionType;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyExemptionType::class, function (Faker $faker) {
    return [
        'nm_tipo' => $faker->words(2, true),
        'descricao' => $faker->words(5, true),
        'ativo' => 1,
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
        'ref_cod_instituicao' =>  factory(LegacyInstitution::class)->state('unique')->make(),
    ];
});
