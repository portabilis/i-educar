<?php

use App\Models\LegacySchoolClassType;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolClassType::class, function (Faker $faker) {

    $name = $faker->colorName;
    $abbreviation = mb_substr($faker->colorName, 0, 5);

    return [
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'nm_tipo' => 'Tipo ' . $name,
        'sgl_tipo' => $abbreviation,
        'data_cadastro' => now(),
    ];
});

$factory->state(LegacySchoolClassType::class, 'unique', function () {

    $schoolClassType = LegacySchoolClassType::query()->first();

    if (empty($schoolClassType)) {
        $schoolClassType = factory(LegacySchoolClassType::class)->create();
    }

    return [
        'cod_turma_tipo' => $schoolClassType->getKey()
    ];
});
