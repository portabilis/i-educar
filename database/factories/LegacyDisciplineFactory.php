<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyInstitution;
use App\Models\LegacyKnowledgeArea;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyDiscipline::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(LegacyInstitution::class)->state('unique')->make(),
        'area_conhecimento_id' => factory(LegacyKnowledgeArea::class)->state('unique')->make(),
        'nome' => $faker->colorName,
        'abreviatura' => $faker->hexColor,
        'tipo_base' => 0,
    ];
});
