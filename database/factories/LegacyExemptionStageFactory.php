<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyExemptionStage::class, function (Faker $faker) {
    $exemption = factory(LegacyDisciplineExemption::class)->create();
    return [
        'ref_cod_dispensa' => $exemption->cod_dispensa,
        'etapa' => 1,
    ];
});
