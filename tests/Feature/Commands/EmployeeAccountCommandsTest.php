<?php

use App\Mail\NewUserMail;
use App\Models\LegacyEmployee;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->employee = LegacyEmployee::query()->findOrFail($this->user->getKey());
    $this->employee->update(['matricula' => 'bia.login', 'ativo' => 1]);
});

test('desativa e ativa o login pela matrícula, pedindo confirmação', function () {
    $this->artisan('disable:login', ['registration' => 'bia.login'])
        ->expectsConfirmation('Deseja realmente desativar o usuário bia.login ?', 'no')
        ->assertExitCode(0);

    expect($this->employee->fresh()->ativo)->toBe(1);

    $this->artisan('disable:login', ['registration' => 'bia.login'])
        ->expectsConfirmation('Deseja realmente desativar o usuário bia.login ?', 'yes')
        ->expectsOutput('Usuário desativado com sucesso!')
        ->assertExitCode(0);

    expect($this->employee->fresh()->ativo)->toBe(0);

    $this->artisan('enable:login', ['registration' => 'bia.login'])
        ->expectsConfirmation('Deseja realmente ativar o usuário bia.login ?', 'no')
        ->assertExitCode(0);
    $this->artisan('enable:login', ['registration' => 'bia.login'])
        ->expectsConfirmation('Deseja realmente ativar o usuário bia.login ?', 'yes')
        ->expectsOutput('Usuário ativado com sucesso!')
        ->assertExitCode(0);

    expect($this->employee->fresh()->ativo)->toBe(1);

    foreach (['disable:login' => 'desativar', 'enable:login' => 'ativar'] as $command => $verbo) {
        $this->artisan($command, ['registration' => 'ninguem'])
            ->expectsConfirmation("Deseja realmente {$verbo} o usuário ninguem ?", 'yes')
            ->expectsOutput('Usuário não encontrado.')
            ->assertExitCode(0);
    }
});

test('troca a senha do usuário nas bases dos clientes e manda a nova por e-mail', function () {
    Mail::fake();
    $pgsql = config('database.connections.pgsql');
    config(['database.connections' => ['pgsql' => $pgsql, 'cliente' => $pgsql]]);

    // A conexão do cliente usa a mesma conexão do teste para enxergar o funcionário
    // criado aqui, inclusive depois de o comando descartá-la.
    DB::extend('cliente', function (array $config, string $name) {
        $connection = app('db.factory')->make($config, $name);
        $connection->setPdo(DB::connection('pgsql')->getPdo());

        return $connection;
    });
    $antiga = $this->employee->senha;

    try {
        $this->artisan('reset:password', ['username' => 'ninguem'])->assertExitCode(0);
        Mail::assertNothingSent();

        $this->artisan('reset:password', ['username' => 'bia.login'])->assertExitCode(0);
    } finally {
        DB::setDefaultConnection('pgsql');
    }

    expect($this->employee->fresh()->senha)->not->toBe($antiga);
    Mail::assertSent(NewUserMail::class);
});

test('gera o token de API do administrador', function () {
    $this->employee->update(['matricula' => 'admin']);

    $this->artisan('admin:token', ['name' => 'integracao'])
        ->expectsOutput('Generated token:')
        ->assertExitCode(0);

    expect(DB::table('personal_access_tokens')->where('name', 'integracao')->where('tokenable_id', $this->user->getKey())->exists())->toBeTrue();
});
