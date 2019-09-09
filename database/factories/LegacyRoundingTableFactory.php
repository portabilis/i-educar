<?php

use App\Models\LegacyRoundingTable;
use Faker\Generator as Faker;
use App\Models\LegacyInstitution;

$factory->define(LegacyRoundingTable::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(LegacyInstitution::class)->state('unique')->make(),
        'nome' => $faker->words(3, true),
        'tipo_nota' => $faker->randomElement([1, 2]),
    ];
});

$factory->defineAs(LegacyRoundingTable::class, 'numeric', function (Faker $faker) use ($factory) {
    $roundingTable = $factory->raw(LegacyRoundingTable::class);

    return array_merge($roundingTable, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
    ]);
});

$factory->defineAs(LegacyRoundingTable::class, 'conceitual', function () use ($factory) {
    $roundingTable = $factory->raw(LegacyRoundingTable::class);

    return array_merge($roundingTable, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
    ]);
});
