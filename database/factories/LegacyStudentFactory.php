<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;

$factory->define(LegacyStudent::class, function () {
    return [
        'ref_idpes' => factory(LegacyIndividual::class)->create(),
        'data_cadastro' => now(),
    ];
});
