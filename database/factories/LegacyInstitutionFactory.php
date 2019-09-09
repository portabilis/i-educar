<?php

use App\Models\LegacyInstitution;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyInstitution::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'ref_idtlog' => 'AV',
        'ref_sigla_uf' => 'SC',
        'cep' => $faker->numerify('########'),
        'cidade' => $faker->city,
        'bairro' => $faker->lastName,
        'logradouro' => $faker->address,
        'nm_responsavel' => $faker->name,
        'data_cadastro' => now(),
        'nm_instituicao' => $faker->company,
    ];
});

$factory->state(LegacyInstitution::class, 'unique', function () {

    $institution = LegacyInstitution::query()->first();

    if (empty($institution)) {
        $institution = factory(LegacyInstitution::class)->create();
    }

    return [
        'cod_instituicao' => $institution->getKey()
    ];
});
