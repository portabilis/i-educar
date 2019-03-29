<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LegacyUser::class, function (Faker $faker) {
    return [
        'cod_usuario' => function () {
            return factory(\App\Models\LegacyEmployee::class)->create()->ref_cod_pessoa_fj;
        },
        'ref_cod_instituicao' => 1,
        'ref_funcionario_cad' => function () {
            return factory(\App\Models\LegacyEmployee::class)->create()->ref_cod_pessoa_fj;
        },
        'ref_cod_tipo_usuario' => function () {
            return factory(\App\Models\LegacyUserType::class)->create()->cod_tipo_usuario;
        },
        'data_cadastro' => $faker->dateTime,
        'ativo' => 1,
    ];
});
