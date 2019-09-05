<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyUser::class, function (Faker $faker) {
    return [
        'cod_usuario' => function () {
            return factory(LegacyEmployee::class)->create()->ref_cod_pessoa_fj;
        },
        'ref_cod_instituicao' => 1,
        'ref_funcionario_cad' => function () {
            return factory(LegacyEmployee::class)->create()->ref_cod_pessoa_fj;
        },
        'ref_cod_tipo_usuario' => function () {
            return factory(LegacyUserType::class)->create()->cod_tipo_usuario;
        },
        'data_cadastro' => $faker->dateTime,
        'ativo' => 1,
    ];
});

$factory->state(LegacyUser::class, 'unique', function () {

    $user = LegacyUser::query()->first();

    if (empty($user)) {
        $user = factory(LegacyUser::class)->create();
    }

    return [
        'cod_usuario' => $user->getKey(),
        'ref_funcionario_cad' => $user->ref_funcionario_cad,
        'ref_cod_tipo_usuario' => $user->cod_tipo_usuario,
    ];
});
