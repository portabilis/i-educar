<?php

use App\Models\Institution;
use Faker\Generator as Faker;

$factory->define(Institution::class, function (Faker $faker) {
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

$factory->state(Institution::class, 'unique', function () {

    $institution = Institution::query()->first();

    if (empty($institution)) {
        $institution = factory(Institution::class)->create();
    }

    return [
        'cod_instituicao' => $institution->getKey()
    ];
});
