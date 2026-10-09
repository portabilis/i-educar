<?php

use App\Events\UserSocialiteNotFound;
use App\Models\LegacyEmployee;
use App\User;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

function usuarioDoProvedor(string $email): SocialiteUser
{
    return (new SocialiteUser)->map(['email' => $email]);
}

function provedorDevolve(SocialiteUser|Throwable $user): void
{
    $provider = Mockery::mock();
    $user instanceof Throwable
        ? $provider->shouldReceive('user')->andThrow($user)
        : $provider->shouldReceive('user')->andReturn($user);

    Socialite::shouldReceive('driver')->with('passport')->andReturn($provider);
}

test('sem autorização ou com a sessão expirada volta ao login', function () {
    $this->get(route('socialite.callback', ['error' => 'access_denied']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['login' => 'Autorização de acesso não concedida.']);

    provedorDevolve(new InvalidStateException);

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['login' => 'Sessão de login expirada. Tente entrar novamente.']);
});

test('avisa quando não acha o usuário nem depois do evento de usuário não encontrado', function () {
    Event::fake([UserSocialiteNotFound::class]);
    provedorDevolve(usuarioDoProvedor('ninguem@exemplo.test'));

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['login' => 'Usuário não encontrado.']);

    Event::assertDispatched(UserSocialiteNotFound::class, fn ($event) => $event->user->getEmail() === 'ninguem@exemplo.test');
});

test('um ouvinte do evento pode vincular o usuário, que então entra', function () {
    $user = LegacyUserFactory::new()->create();
    Event::listen(UserSocialiteNotFound::class, fn ($event) => LegacyEmployee::query()->whereKey($user->getKey())->update(['email' => $event->user->getEmail()]));
    provedorDevolve(usuarioDoProvedor('novo@exemplo.test'));

    $this->get(route('socialite.callback', ['code' => 'abc']))->assertRedirect('/');

    $this->assertAuthenticatedAs(User::query()->find($user->getKey()));
});

test('usuário inativo vê o motivo da inativação ou a mensagem padrão', function () {
    $user = LegacyUserFactory::new()->create();
    LegacyEmployee::query()->whereKey($user->getKey())->update(['email' => 'inativo@exemplo.test', 'ativo' => 0, 'motivo' => 'Desligado da rede']);
    provedorDevolve(usuarioDoProvedor('inativo@exemplo.test'));

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['login' => 'Desligado da rede']);

    LegacyEmployee::query()->whereKey($user->getKey())->update(['motivo' => null]);

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertSessionHasErrors(['login' => __('auth.inactive')]);

    $this->assertGuest();
});

test('entra com o usuário ativo quando um inativo tem o mesmo e-mail', function () {
    $email = 'mesmo.email@exemplo.test';

    $inativo = LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);
    $ativo = LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 1])->ref_cod_pessoa_fj,
        'ativo' => 1,
    ]);
    provedorDevolve(usuarioDoProvedor($email));

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertSessionDoesntHaveErrors('login');

    $this->assertAuthenticatedAs(User::query()->find($ativo->cod_usuario));
    expect(User::query()->find($inativo->cod_usuario)->isInactive())->toBeTrue();
});

test('segue recusando quando o único usuário com o e-mail está inativo', function () {
    $email = 'so.inativo@exemplo.test';

    LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);
    provedorDevolve(usuarioDoProvedor($email));

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('não dispara o evento de usuário não encontrado para uma conta desativada', function () {
    Event::fake([UserSocialiteNotFound::class]);
    $email = 'desativado@portabilis.com.br';

    LegacyUserFactory::new()->create([
        'cod_usuario' => LegacyEmployeeFactory::new()->create(['email' => $email, 'ativo' => 0])->ref_cod_pessoa_fj,
        'ativo' => 0,
    ]);
    provedorDevolve(usuarioDoProvedor($email));

    $this->get(route('socialite.callback', ['code' => 'abc']))
        ->assertRedirect('/login')
        ->assertSessionHasErrors('login');

    $this->assertGuest();
    Event::assertNotDispatched(UserSocialiteNotFound::class);
    expect(LegacyEmployee::query()->where('email', $email)->count())->toBe(1);
});
