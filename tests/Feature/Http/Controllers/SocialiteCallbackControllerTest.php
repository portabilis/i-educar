<?php

use App\Models\LegacyEmployee;
use App\User;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

uses(TestCase::class);

test('callback with invalid state redirects to login', function () {
    $response = $this->withSession(['state' => 'expected-state'])
        ->get('/auth/callback?code=code&state=other-state');

    $response->assertRedirect('/login')
        ->assertSessionHasErrors([
            'login' => 'Sessão de login expirada. Tente entrar novamente.',
        ]);
});

test('callback without state in session redirects to login', function () {
    $response = $this->get('/auth/callback?code=code&state=reused-state');

    $response->assertRedirect('/login')
        ->assertSessionHasErrors('login');
});

function socialiteRetornaEmail(string $email): void
{
    $socialite = Mockery::mock(Laravel\Socialite\Contracts\User::class);
    $socialite->shouldReceive('getEmail')->andReturn($email);
    $socialite->shouldReceive('getName')->andReturn('Pessoa de Teste');
    $socialite->shouldReceive('getNickname')->andReturn('pessoa.teste');

    Socialite::shouldReceive('driver->user')->andReturn($socialite);
}

test('callback logs in the active user when an inactive user shares the e-mail', function () {
    $email = 'mesmo.email.' . uniqid() . '@example.com';

    $inativo = LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);
    $ativo = LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 1])->ref_cod_pessoa_fj,
        'ativo' => 1,
    ]);

    socialiteRetornaEmail($email);

    $this->get('/auth/callback?code=code&state=state')
        ->assertSessionDoesntHaveErrors('login');

    $this->assertAuthenticatedAs(User::find($ativo->cod_usuario));
    expect(User::find($inativo->cod_usuario)->isInactive())->toBeTrue();
});

test('callback keeps refusing when the only user with the e-mail is inactive', function () {
    $email = 'so.inativo.' . uniqid() . '@example.com';

    LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);

    socialiteRetornaEmail($email);

    $this->get('/auth/callback?code=code&state=state')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('callback does not create a new user for a deactivated Portabilis account', function () {
    $email = 'desativado.' . uniqid() . '@portabilis.com.br';

    LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);

    socialiteRetornaEmail($email);

    $this->get('/auth/callback?code=code&state=state')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('login');

    $this->assertGuest();
    expect(LegacyEmployee::query()->where('email', $email)->count())->toBe(1);
});
