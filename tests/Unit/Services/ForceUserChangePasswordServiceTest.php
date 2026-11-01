<?php

use App\Services\ForceUserChangePasswordService;
use App\User;
use Tests\TestCase;

uses(TestCase::class);

// Usuário com o tempo desde a última troca de senha informado.
function usuarioComSenhaDe(int $days, bool $admin = false): User
{
    $employee = Mockery::mock();
    $employee->force_reset_password = false;
    $employee->shouldReceive('save')->zeroOrMoreTimes();

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('isAdmin')->andReturn($admin);
    $user->shouldReceive('getDaysSinceLastPasswordUpdated')->andReturn($days);
    $user->setRelation('employee', $employee);

    return $user;
}

test('obriga a trocar a senha quando passou do prazo, menos para o administrador', function () {
    config(['legacy.app.user_accounts.default_password_expiration_period' => 30]);
    $service = app(ForceUserChangePasswordService::class);

    $service->execute($expired = usuarioComSenhaDe(31));
    $service->execute($recent = usuarioComSenhaDe(5));
    $service->execute($admin = usuarioComSenhaDe(90, admin: true));

    expect($expired->employee->force_reset_password)->toBeTrue()
        ->and($recent->employee->force_reset_password)->toBeFalse()
        ->and($admin->employee->force_reset_password)->toBeFalse();
});
