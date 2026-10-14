<?php

use App\Events\UserUpdated;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Event::fake([UserUpdated::class]);
    $this->employee = LegacyEmployeeFactory::new()->create(['senha' => Hash::make('Antiga@123')]);
    $this->user = LegacyUserFactory::new()->admin()->create(['cod_usuario' => $this->employee->ref_cod_pessoa_fj]);
    $this->actingAs($this->user);

    $this->change = fn (array $data) => $this->from(route('change-password'))->post(route('post-change-password'), $data + ['login' => $this->employee->matricula]);
});

test('a tela de troca de senha abre', function () {
    $this->get(route('change-password'))->assertOk()->assertViewIs('password.change');
});

test('a troca exige a senha confirmada, forte e diferente da atual', function () {
    ($this->change)(['password' => 'Nova@1234', 'password_confirmation' => 'Outra@1234'])
        ->assertSessionHasErrors(['password' => 'As senhas não são iguais.']);

    ($this->change)(['password' => 'fraca', 'password_confirmation' => 'fraca'])
        ->assertSessionHasErrors(['password' => 'A senha deve conter pelo menos 8 caracteres e uma combinação de letras maiúsculas e minúsculas, números e símbolos (!@#$%*).']);

    ($this->change)(['password' => 'Antiga@123', 'password_confirmation' => 'Antiga@123'])
        ->assertSessionHasErrors(['password' => 'A senha informada foi usada recentemente. Por favor, escolha outra.']);
});

test('troca a senha do funcionário e avisa a atualização do usuário', function () {
    ($this->change)(['password' => 'Nova@1234', 'password_confirmation' => 'Nova@1234'])
        ->assertRedirect('/');

    expect(Hash::check('Nova@1234', $this->employee->refresh()->senha))->toBeTrue()
        ->and((bool) $this->employee->force_reset_password)->toBeFalse();
    Event::assertDispatched(UserUpdated::class);
});

test('a troca falha quando a matrícula não é a do usuário', function () {
    ($this->change)(['login' => 'outra-matricula', 'password' => 'Nova@1234', 'password_confirmation' => 'Nova@1234'])
        ->assertRedirect(route('change-password'))
        ->assertSessionHasErrors(['email' => 'Não encontramos um usuário com esta matrícula.']);
});
