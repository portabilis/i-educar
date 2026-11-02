<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyUserType;
use App\Process;
use App\User;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    // Como nas bases reais, o menu do processo tem o mesmo número do processo.
    $this->menu = MenuFactory::new()->create(['id' => Process::USER_TYPES, 'process' => Process::USER_TYPES]);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));
});

test('mostra o tipo de usuário com os processos que ele já tem', function () {
    $userType = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]);
    $menu = $this->menu;
    LegacyMenuUserTypeFactory::new()->create(['ref_cod_tipo_usuario' => $userType, 'menu_id' => $menu, 'cadastra' => 1, 'visualiza' => 1, 'exclui' => 0]);
    LegacyMenuUserTypeFactory::new()->create(['ref_cod_tipo_usuario' => $this->user->ref_cod_tipo_usuario, 'menu_id' => $menu, 'cadastra' => 1, 'visualiza' => 1, 'exclui' => 1]);

    $this->get(route('usertype.show', $userType))
        ->assertOk()
        ->assertViewHas('processes', fn ($processes) => $processes[Process::USER_TYPES] === LegacyUserType::CAN_MODIFY);
});

test('cadastra, edita e exclui tipos de usuário e avisa quando não consegue', function () {
    $this->post(route('usertype.create'), ['name' => 'Secretaria', 'level' => LegacyUserType::LEVEL_SCHOOLING, 'processes' => [Process::USER_TYPES => LegacyUserType::CAN_REMOVE]])
        ->assertRedirect(route('usertype.index'))
        ->assertSessionHas('success', 'Registro cadastrado com sucesso.');

    $userType = LegacyUserType::query()->where('nm_tipo', 'Secretaria')->firstOrFail();

    $this->put(route('usertype.update', $userType), ['name' => 'Secretaria escolar', 'level' => LegacyUserType::LEVEL_SCHOOLING])
        ->assertRedirect(route('usertype.index'))
        ->assertSessionHas('success', 'Registro editado com sucesso.');

    expect($userType->refresh()->nm_tipo)->toBe('Secretaria escolar');

    $this->post(route('usertype.create'), ['level' => LegacyUserType::LEVEL_SCHOOLING])
        ->assertRedirect(route('usertype.new'))
        ->assertSessionHas('error', 'Não foi possível cadastrar o registro.');

    $this->put(route('usertype.update', $userType), ['name' => null, 'level' => LegacyUserType::LEVEL_SCHOOLING])
        ->assertRedirect(route('usertype.new'))
        ->assertSessionHas('error', 'Não foi possível editar o registro.');

    $this->delete(route('usertype.delete', $userType))
        ->assertRedirect(route('usertype.index'))
        ->assertSessionHas('success', 'Registro excluído com sucesso.');

    $inUse = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]);
    LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $inUse]);
    $inUse->menus()->detach();

    $this->delete(route('usertype.delete', $inUse))
        ->assertRedirect(route('usertype.show', ['userType' => $inUse]))
        ->assertSessionHas('error', 'Não foi possível excluir o registro.');
});
