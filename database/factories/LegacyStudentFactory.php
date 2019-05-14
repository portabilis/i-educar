<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyStudent::class, function () {
    return [
        'ref_idpes' => factory(LegacyIndividual::class)->create(),
        'data_cadastro' => now(),
    ];
});
