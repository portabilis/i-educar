<?php

use App\Notifications\ResetPasswordNotification;
use App\User;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Notification::fake();
    $this->employee = LegacyEmployeeFactory::new()->create(['senha' => Hash::make('Antiga@123'), 'ativo' => 1]);
    $this->user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create(['cod_usuario' => $this->employee->ref_cod_pessoa_fj])->getKey());
});

test('o pedido de nova senha envia o link para a matrícula conhecida e recusa a desconhecida', function () {
    $this->get('/password/reset')->assertOk();

    $this->from('/password/reset')->post('/password/email', ['login' => $this->employee->matricula])
        ->assertRedirect('/password/reset')
        ->assertSessionHas('status');

    $this->from('/password/reset')->post('/password/email', ['login' => 'nao-existe'])
        ->assertRedirect('/password/reset')
        ->assertSessionHasErrors('email');
});

test('redefine a senha com o token, autentica e avisa o usuário', function () {
    $token = Password::broker()->createToken($this->user);

    $this->get("/password/reset/{$token}")->assertOk();

    $this->from("/password/reset/{$token}")->post('/password/reset', ['token' => $token, 'login' => $this->employee->matricula, 'password' => 'curta', 'password_confirmation' => 'curta'])
        ->assertSessionHasErrors('password');

    $this->post('/password/reset', ['token' => $token, 'login' => $this->employee->matricula, 'password' => 'Nova@1234', 'password_confirmation' => 'Nova@1234'])
        ->assertRedirect('/');

    expect(Hash::check('Nova@1234', $this->employee->refresh()->senha))->toBeTrue();
    $this->assertAuthenticated();
    Notification::assertSentTo($this->user, ResetPasswordNotification::class);
});

test('a redefinição exige token, matrícula e senha confirmada', function () {
    $this->from('/password/reset/x')->post('/password/reset', ['password' => 'Nova@1234', 'password_confirmation' => 'Outra'])
        ->assertSessionHasErrors(['token', 'login', 'password' => 'As senhas não são iguais.']);
});
