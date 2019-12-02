<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacySchool;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyTransferRequest::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        // 'ref_cod_matricula_saida' => factory(LegacyEnrollment::class)->create(),
        'observacao' => $faker->words(3, true),
        'ref_cod_escola_destino' => factory(LegacySchool::class)->create(),
        'data_cadastro' => now(),
        'ativo' =>1,
        'ref_cod_transferencia_tipo'=> factory(LegacyTransferType::class)->create()
    ];
});
