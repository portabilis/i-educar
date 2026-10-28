<?php

use App\Menu;
use App\Models\LegacyUserType;
use App\Process;
use App\User;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Security\ActivatesSystem;
use Tests\TestCase;

uses(TestCase::class, ActivatesSystem::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->schoolUserManagingUserTypes = function (): User {
        $user = LegacyUserFactory::new()
            ->withAccess(Process::USER_TYPES)
            ->create([
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]),
            ]);

        $user = User::query()->findOrFail($user->getKey());
        $user->employee->update(['data_troca_senha' => now(), 'force_reset_password' => false]);

        return $user;
    };

    $this->activateSystem();
});

test('nao permite elevar o nivel do proprio tipo', function () {
    $user = ($this->schoolUserManagingUserTypes)();
    $userType = $user->type;

    $this->actingAs($user)
        ->put('/usuarios/tipos/' . $userType->getKey(), [
            'name' => $userType->nm_tipo,
            'level' => LegacyUserType::LEVEL_ADMIN,
        ])
        ->assertForbidden();

    $this->assertSame(LegacyUserType::LEVEL_SCHOOLING, $userType->refresh()->nivel);
});

test('nao permite criar tipo com nivel superior', function () {
    $user = ($this->schoolUserManagingUserTypes)();

    $this->actingAs($user)
        ->post('/usuarios/tipos', [
            'name' => 'Tipo com nível superior',
            'level' => LegacyUserType::LEVEL_INSTITUTIONAL,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing(LegacyUserType::class, ['nm_tipo' => 'Tipo com nível superior']);
});

test('nao permite editar tipo de nivel superior', function () {
    $user = ($this->schoolUserManagingUserTypes)();
    $adminType = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_ADMIN]);

    $this->actingAs($user)
        ->put('/usuarios/tipos/' . $adminType->getKey(), [
            'name' => $adminType->nm_tipo,
            'level' => LegacyUserType::LEVEL_SCHOOLING,
        ])
        ->assertForbidden();

    $this->assertSame(LegacyUserType::LEVEL_ADMIN, $adminType->refresh()->nivel);
});

test('limita as permissoes concedidas as do proprio usuario', function () {
    $user = ($this->schoolUserManagingUserTypes)();

    /** @var Menu $viewOnlyMenu */
    $viewOnlyMenu = MenuFactory::new()->create();
    LegacyMenuUserTypeFactory::new()->create([
        'menu_id' => $viewOnlyMenu,
        'ref_cod_tipo_usuario' => $user->type,
        'visualiza' => 1,
        'cadastra' => 0,
        'exclui' => 0,
    ]);

    /** @var Menu $foreignMenu */
    $foreignMenu = MenuFactory::new()->create();

    $this->actingAs($user)
        ->post('/usuarios/tipos', [
            'name' => 'Tipo de nível escola',
            'level' => LegacyUserType::LEVEL_SCHOOLING,
            'processes' => [
                $viewOnlyMenu->getKey() => LegacyUserType::CAN_REMOVE,
                $foreignMenu->getKey() => LegacyUserType::CAN_REMOVE,
            ],
        ])
        ->assertRedirect();

    $created = LegacyUserType::query()->where('nm_tipo', 'Tipo de nível escola')->firstOrFail();

    $this->assertDatabaseHas('pmieducar.menu_tipo_usuario', [
        'ref_cod_tipo_usuario' => $created->getKey(),
        'menu_id' => $viewOnlyMenu->getKey(),
        'visualiza' => 1,
        'cadastra' => 0,
        'exclui' => 0,
    ]);

    $this->assertDatabaseHas('pmieducar.menu_tipo_usuario', [
        'ref_cod_tipo_usuario' => $created->getKey(),
        'menu_id' => $foreignMenu->getKey(),
        'visualiza' => 0,
        'cadastra' => 0,
        'exclui' => 0,
    ]);
});

test('permite administrador criar tipo de qualquer nivel', function () {
    $admin = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());

    $this->actingAs($admin)
        ->post('/usuarios/tipos', [
            'name' => 'Tipo poli-institucional',
            'level' => LegacyUserType::LEVEL_ADMIN,
        ])
        ->assertRedirect(route('usertype.index'));

    $this->assertDatabaseHas(LegacyUserType::class, [
        'nm_tipo' => 'Tipo poli-institucional',
        'nivel' => LegacyUserType::LEVEL_ADMIN,
    ]);
});
