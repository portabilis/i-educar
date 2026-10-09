<?php

use App\Http\Middleware\SkipMiddleware;
use App\Http\Middleware\TrustHosts;
use App\Models\Announcement;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('quem não está autenticado vai para o login, ou recebe 401 em json', function () {
    $this->get('/user')->assertRedirect(route('login'));
    $this->getJson('/user')->assertUnauthorized();
});

test('quem já está autenticado não volta à tela de login', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->get('/login')->assertRedirect('/');
});

test('sem a configuração ativa, quem não é administrador cai na tela de sistema suspenso', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());

    $this->get('/user')->assertRedirect('/intranet/suspenso.php');
});

test('quem precisa trocar a senha é levado à troca de senha', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $user->employee->update(['force_reset_password' => true]);
    $this->actingAs($user);

    $this->get('/user')->assertRedirect(route('change-password'));
});

test('um aviso que exige ciência barra a navegação até a confirmação', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $announcement = Announcement::query()->create(['name' => 'Aviso', 'description' => 'x', 'show_confirmation' => true, 'created_by_user_id' => $user->getKey()]);
    $announcement->userTypes()->sync([$user->ref_cod_tipo_usuario]);

    $this->get('/user')
        ->assertRedirect(route('announcement.user.show'))
        ->assertSessionHas('error', 'Confirme a ciência do aviso antes de prosseguir!');

    $announcement->users()->sync([$user->getKey() => ['confirmed_at' => now()]]);

    $this->get('/user')->assertOk();
});

test('os hosts confiáveis são os subdomínios do endereço da aplicação, e o middleware vazio só repassa', function () {
    expect((new TrustHosts(app()))->hosts())->toHaveCount(1)
        ->and((new SkipMiddleware)->handle(Request::create('/'), fn () => 'ok'))->toBe('ok');
});
