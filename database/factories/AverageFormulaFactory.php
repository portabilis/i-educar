<?php

use App\Models\AverageFormula;
use App\Models\Institution;
use Faker\Generator as Faker;

$factory->define(AverageFormula::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(Institution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'formula_media' => 'Se / Et',
    ];
});
