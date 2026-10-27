<?php

use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use App\User;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Feature\Security\ActivatesSystem;
use Tests\TestCase;

uses(TestCase::class, ActivatesSystem::class, DatabaseTransactions::class, WithoutMiddleware::class);

beforeEach(function () {
    $this->schoolUserManagingUsers = function (): User {
        $user = LegacyUserFactory::new()
            ->withAccess(555)
            ->create([
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]),
            ]);

        $user = User::query()->findOrFail($user->getKey());
        $user->employee->update(['data_troca_senha' => now(), 'force_reset_password' => false]);

        return $user;
    };

    $this->activateSystem();
});

test('nao permite atribuir tipo de nivel superior', function () {
    $user = ($this->schoolUserManagingUsers)();
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
});
