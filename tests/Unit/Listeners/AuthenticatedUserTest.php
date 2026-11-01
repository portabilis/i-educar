<?php

use App\Listeners\AuthenticatedUser;
use App\Models\LegacyEmployee;
use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use App\User;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->disabler = Mockery::mock(DisableUsersWithDaysGoneSinceLastAccessService::class);
    $this->listener = new AuthenticatedUser($this->disabler);

    // Usuário autenticado com os dados de servidor informados.
    $this->authenticated = function (array $employee = []) {
        $user = LegacyUserFactory::new()->create();
        if ($employee) {
            LegacyEmployee::query()->whereKey($user->getKey())->update($employee);
        }

        // Sem actingAs: o próprio login dispararia o evento e este ouvinte.
        return new Authenticated('web', User::query()->findOrFail($user->getKey()));
    };

    $this->message = function (Authenticated $event) {
        try {
            $this->listener->handle($event);
        } catch (ValidationException $exception) {
            return $exception->validator->errors()->first($event->user->login);
        }

        return null;
    };
});

test('usuário inativo sai com o motivo da inativação ou a mensagem padrão', function () {
    expect(($this->message)(($this->authenticated)(['ativo' => 0, 'motivo' => 'Licença'])))->toBe('Licença')
        ->and(($this->message)(($this->authenticated)(['ativo' => 0, 'motivo' => null])))->toBe(__('auth.inactive'));
});

test('usuário que ainda não começou sai com a data de início', function () {
    expect(($this->message)(($this->authenticated)(['data_inicial' => now()->addDays(2)->format('Y-m-d')])))
        ->toBe(__('auth.not_started', ['date' => now()->addDays(2)->format('d/m/Y')]));
});

test('usuário expirado sai depois de conferir o tempo sem acesso', function () {
    $this->disabler->shouldReceive('execute')->once();

    expect(($this->message)(($this->authenticated)(['data_expiracao' => now()->subDay()->format('Y-m-d')])))->toBe(__('auth.expired'));
});

test('usuário válido passa sem erro', function () {
    $this->disabler->shouldReceive('execute')->once();

    expect(($this->message)(($this->authenticated)()))->toBeNull();
});
