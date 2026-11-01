<?php

use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('school user does not get students of another school', function () {
    $user = LegacyUserFactory::new()->create([
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::ESCOLA]),
    ]);

    LegacyUserSchoolFactory::new()->create([
        'ref_cod_usuario' => $user,
        'ref_cod_escola' => LegacySchoolFactory::new()->create(),
    ]);

    $params = [
        'oper' => 'get',
        'resource' => 'alunos',
        'escola' => LegacySchoolFactory::new()->create()->getKey(),
        'tipo' => 'admitidos',
        'ano' => now()->year,
        'data_inicial' => now()->startOfYear()->format('Y-m-d'),
        'data_final' => now()->format('Y-m-d'),
    ];

    $_GET = $params;

    $this->actingAs($user)
        ->get('/module/Api/ConsultaMovimentoGeral?' . http_build_query($params))
        ->assertJsonPath('alunos', [])
        ->assertJsonFragment(['msg' => 'Usuário sem acesso à escola informada.']);
});
