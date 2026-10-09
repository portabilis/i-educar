<?php

use App\Models\LegacyAccess;
use App\Models\LegacyEmployee;
use App\User;
use Database\Factories\LegacyAccessFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Carbon::setTestNow('2030-06-15 12:00:00');

    $this->user = User::query()->findOrFail(LegacyUserFactory::new()->institutional()->create()->getKey());
});

afterEach(function () {
    Carbon::setTestNow();
});

test('a senha, já cifrada por quem chama, é gravada no funcionário', function () {
    $hash = Hash::make('Senha@Nova123');
    $this->user->password = $hash;

    expect(LegacyEmployee::query()->whereKey($this->user->getKey())->value('senha'))->toBe($hash);
});

test('o acesso expira depois da data de expiração e começa na data inicial', function () {
    expect($this->user->isExpired())->toBeFalse()
        ->and($this->user->isNotStarted())->toBeFalse();

    $this->user->employee->update(['data_expiracao' => '2030-06-01', 'data_inicial' => '2030-07-01']);
    $this->user->refresh();

    expect($this->user->isExpired())->toBeTrue()
        ->and($this->user->isNotStarted())->toBeTrue();
});

// Com o Carbon 3, `diffInDays()` devolve a diferença com sinal: para datas no
// passado o resultado é negativo, e quem compara com um limite de dias nunca o
// alcança. Os testes registram o valor que o método devolve hoje.
test('conta os dias desde o último acesso, a reativação da conta ou a criação', function () {
    expect($this->user->getLastAccessDate()->toDateString())->toBe($this->user->getCreatedAtCustom()->toDateString());

    $access = LegacyAccessFactory::new()->create(['people_id' => $this->user->getKey()]);
    LegacyAccess::query()->whereKey($access->getKey())->update(['data_hora' => '2030-06-05 12:00:00']);

    expect($this->user->access()->exists())->toBeTrue()
        ->and($this->user->getDaysSinceLastAccessOrEnabledUserDate())->toBe(-10);

    $this->user->employee->update(['data_reativa_conta' => '2030-06-10 12:00:00']);
    $this->user->refresh();

    expect($this->user->getDaysSinceLastAccessOrEnabledUserDate())->toBe(-5)
        ->and($this->user->getEnabledUserDate()->toDateString())->toBe('2030-06-10');
});

test('conta os dias desde a última troca de senha', function () {
    $this->user->employee->update(['data_troca_senha' => '2030-06-01 12:00:00']);
    $this->user->refresh();

    expect($this->user->getDaysSinceLastPasswordUpdated())->toBe(-14)
        ->and($this->user->getPasswordUpdatedDate()->toDateString())->toBe('2030-06-01');
});

test('sem funcionário não há datas de reativação nem de troca de senha', function () {
    $user = new User;

    expect($user->getEnabledUserDate())->toBeNull()
        ->and($user->getPasswordUpdatedDate())->toBeNull();
});

test('lista os usuários ativos e desativa um deles', function () {
    // O filtro dos administradores compara o código do tipo de usuário com o
    // nível de administrador, então só exclui o tipo de código 1.
    $active = (new User)->getActiveUsersNotAdmin();

    expect($active->modelKeys())->toContain($this->user->getKey())
        ->and($this->user->menu()->get())->toBeInstanceOf(Collection::class);

    $this->user->disable();

    expect($this->user->fresh()->ativo)->toBe(0)
        ->and($this->user->employee->fresh()->ativo)->toBe(0)
        ->and($this->user->employee->fresh()->data_expiracao)->toStartWith('2030-06-15');
});
