<?php

use App\Models\Announcement;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->employee = LegacyEmployeeFactory::new()->create(['senha' => Hash::make('Senha@123'), 'ativo' => 1]);
    $this->user = LegacyUserFactory::new()->admin()->create(['cod_usuario' => $this->employee->ref_cod_pessoa_fj]);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $this->login = fn (string $password = 'Senha@123') => $this->from('/login')->post('/login', ['login' => $this->employee->matricula, 'password' => $password]);
    $this->announcement = fn (array $attributes = []) => tap(Announcement::query()->create($attributes + ['name' => 'Aviso', 'description' => 'x', 'created_by_user_id' => $this->user->getKey()]), fn ($announcement) => $announcement->userTypes()->sync([$this->user->ref_cod_tipo_usuario]));
});

test('a tela de login abre, também quando forçada', function () {
    $this->get('/login')->assertOk();
    $this->get('/login?force=1')->assertOk();
});

test('o login exige matrícula e senha', function () {
    $this->from('/login')->post('/login', [])
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['login' => 'O campo matrícula é obrigatório.', 'password' => 'O campo senha é obrigatório.']);
});

test('autentica com a matrícula e a senha do funcionário e sai', function () {
    ($this->login)()->assertRedirect('/');
    $this->assertAuthenticatedAs($this->user->refresh());

    $this->post('/logout')->assertRedirect('/');
    $this->assertGuest();
});

test('recusa a senha errada e bloqueia depois de duas tentativas', function () {
    ($this->login)('errada')->assertRedirect('/login')->assertSessionHasErrors('login');
    $this->assertGuest();

    ($this->login)('errada');
    ($this->login)('errada')->assertSessionHasErrors('login');
});

test('recusa o usuário inativo com o motivo', function () {
    $this->employee->update(['ativo' => 0, 'motivo' => 'Desligado']);

    // A mensagem fica na chave com a matrícula.
    ($this->login)()->assertSessionHasErrors();
    expect(session('errors')->all())->toBe(['Desligado']);
    $this->assertGuest();
});

test('com um aviso que se repete a cada login, vai para o aviso e desfaz a confirmação', function () {
    $announcement = ($this->announcement)(['repeat_on_login' => true, 'show_confirmation' => true]);
    $announcement->users()->sync([$this->user->getKey() => ['read_at' => now(), 'confirmed_at' => now()]]);

    ($this->login)()->assertRedirect(route('announcement.user.show'));

    expect($announcement->users()->whereKey($this->user->getKey())->first()->pivot->confirmed_at)->toBeNull();
});

test('com um aviso ainda não lido vai para o aviso; já lido, segue para a página inicial', function () {
    $announcement = ($this->announcement)();

    ($this->login)()->assertRedirect(route('announcement.user.show'));

    $this->post('/logout');
    $announcement->users()->sync([$this->user->getKey() => ['read_at' => now()]]);

    ($this->login)()->assertRedirect('/');
});
