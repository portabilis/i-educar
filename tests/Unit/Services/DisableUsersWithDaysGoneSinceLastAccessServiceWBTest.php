<?php

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->createService = function ($expirationPeriod) {
        $config = Mockery::mock(Repository::class);

        $config->shouldReceive('get')
            ->andReturn($expirationPeriod);

        return new DisableUsersWithDaysGoneSinceLastAccessService($config);
    };

    $this->createUser = function (bool $isAdmin, int $daysGone) {
        $user = Mockery::mock(Authenticatable::class);

        $user->disabled = false;

        $user->shouldReceive('isAdmin')
            ->andReturn($isAdmin);

        $user->shouldReceive('getDaysSinceLastAccessOrEnabledUserDate')
            ->andReturn($daysGone);

        $user->shouldReceive('disable')
            ->andReturnUsing(function () use ($user) {
                $user->disabled = true;
            });

        return $user;
    };
});

afterEach(function () {
    Mockery::close();
});

// CT01 - expirationPeriod vazio
test('nao desabilita quando expiration period esta vazio', function () {
    $service = ($this->createService)(0);
    $user = ($this->createUser)(false, 100);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT02 - expirationPeriod > daysGone
test('nao desabilita quando days gone e menor que expiration period', function () {
    $service = ($this->createService)(10);
    $user = ($this->createUser)(false, 1);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT03 - expirationPeriod < daysGone
test('desabilita quando days gone e maior que expiration period', function () {
    $service = ($this->createService)(10);
    $user = ($this->createUser)(false, 11);

    $service->execute($user);

    $this->assertTrue($user->disabled);
});

// CT04 - expirationPeriod == daysGone
test('desabilita quando days gone e igual ao expiration period', function () {
    $service = ($this->createService)(10);
    $user = ($this->createUser)(false, 10);

    $service->execute($user);

    $this->assertTrue($user->disabled);
});

// CT05 - administrador
test('nao desabilita usuario administrador ativo', function () {
    $service = ($this->createService)(30);
    $user = ($this->createUser)(true, 10);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT06 - administrador com muitos dias sem acesso
test('nao desabilita usuario administrador inativo', function () {
    $service = ($this->createService)(30);
    $user = ($this->createUser)(true, 45);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});
