<?php

namespace Tests\Feature\Security;

use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use App\User;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Resíduo da CVE-2024-45058: quem tem permissão no cadastro de usuários
 * conseguia, por um POST montado à mão, atribuir um tipo de usuário de nível
 * superior ao seu, que o formulário não oferece.
 */
class UserTypeAssignmentTest extends TestCase
{
    use ActivatesSystem, DatabaseTransactions, WithoutMiddleware;

    private const PROCESSO_USUARIOS = 555;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activateSystem();
    }

    private function schoolUserManagingUsers(): User
    {
        $user = LegacyUserFactory::new()
            ->withAccess(self::PROCESSO_USUARIOS)
            ->create([
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]),
            ]);

        $user = User::query()->findOrFail($user->getKey());
        $user->employee->update(['data_troca_senha' => now(), 'force_reset_password' => false]);

        return $user;
    }

    public function test_nao_permite_atribuir_tipo_de_nivel_superior()
    {
        $user = $this->schoolUserManagingUsers();
        $target = LegacyUserFactory::new()->create([
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]),
        ]);
        $originalType = $target->ref_cod_tipo_usuario;
        $adminType = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_ADMIN]);

        $_POST = [
            'tipoacao' => 'Editar',
            'ref_pessoa' => $target->getKey(),
            'ref_cod_tipo_usuario' => $adminType->getKey(),
            'matricula' => 'm' . $target->getKey(),
            'ativo' => 1,
        ];

        $response = $this->actingAs($user)->post('/intranet/educar_usuario_cad.php?ref_pessoa=' . $target->getKey(), $_POST);

        $this->assertSame($originalType, LegacyUser::query()->whereKey($target->getKey())->value('ref_cod_tipo_usuario'));
        $response->assertSee('Você não tem permissão para atribuir este tipo de usuário.', false);
    }
}
