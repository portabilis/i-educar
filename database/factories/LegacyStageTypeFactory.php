<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyStageType;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyStageType::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'nm_tipo' => $faker->word,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->state('unique')->make(),
        'num_etapas' => $faker->numberBetween(1, 4),
    ];
});

$factory->state(LegacyStageType::class, 'unique', function () {

    $stageType = LegacyStageType::query()->first();

    if (empty($stageType)) {
        $stageType = factory(LegacyStageType::class)->create();
    }

    return $stageType->toArray();
});
