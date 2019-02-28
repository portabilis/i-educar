<?php

use App\Models\LegacyAverageFormula;
use App\Models\LegacyInstitution;
use Faker\Generator as Faker;

$factory->define(LegacyAverageFormula::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(LegacyInstitution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'formula_media' => 'Se / Et',
    ];
});
