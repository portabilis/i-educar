<?php

namespace Tests\Api;

use App_Model_NivelTipoUsuario;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Portabilis_Utils_User;
use Tests\TestCase;

class ConsultaMovimentoGeralTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // O usuário e o nível de acesso ficam em cache estático entre as requisições do mesmo processo.
        Portabilis_Utils_User::$_currentUserId = null;
        Portabilis_Utils_User::$_nivelAcesso = null;
        Portabilis_Utils_User::$_permissoes = null;
    }

    public function test_school_user_does_not_get_students_of_another_school(): void
    {
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
    }
}
