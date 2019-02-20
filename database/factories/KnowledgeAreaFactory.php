<?php

use App\Models\Institution;
use App\Models\KnowledgeArea;
use Faker\Generator as Faker;

$factory->define(KnowledgeArea::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(Institution::class)->state('unique')->make(),
        'nome' => $faker->words(3, true),
    ];
});

$factory->state(KnowledgeArea::class, 'unique', function () {

    $knowledgeArea = KnowledgeArea::query()->first();

    if (empty($knowledgeArea)) {
        $knowledgeArea = factory(KnowledgeArea::class)->create();
    }

    return [
        'id' => $knowledgeArea->getKey()
    ];
});
