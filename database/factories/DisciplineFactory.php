<?php

use App\Models\Discipline;
use App\Models\Institution;
use App\Models\KnowledgeArea;
use Faker\Generator as Faker;

$factory->define(Discipline::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(Institution::class)->state('unique')->make(),
        'area_conhecimento_id' => factory(KnowledgeArea::class)->state('unique')->make(),
        'nome' => $faker->colorName,
        'abreviatura' => $faker->hexColor,
        'tipo_base' => 0,
    ];
});
