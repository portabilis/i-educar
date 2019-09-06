<?php

use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyEducationNetwork::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'nm_rede' => $faker->company,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->state('unique')->make(),
    ];
});
