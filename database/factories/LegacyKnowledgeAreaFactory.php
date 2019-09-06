<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyKnowledgeArea;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyKnowledgeArea::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(LegacyInstitution::class)->state('unique')->make(),
        'nome' => $faker->words(3, true),
    ];
});

$factory->state(LegacyKnowledgeArea::class, 'unique', function () {

    $knowledgeArea = LegacyKnowledgeArea::query()->first();

    if (empty($knowledgeArea)) {
        $knowledgeArea = factory(LegacyKnowledgeArea::class)->create();
    }

    return [
        'id' => $knowledgeArea->getKey()
    ];
});
