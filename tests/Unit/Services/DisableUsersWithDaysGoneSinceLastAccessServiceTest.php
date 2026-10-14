<?php

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->createService = function () {
        $config = Mockery::mock(Repository::class);

        $config->shouldReceive('get')
            ->andReturn(45);

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

// CT01
test('nao desabilita usuario com 44 dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(false, 44);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT02
test('desabilita usuario com 45 dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(false, 45);

    $service->execute($user);

    $this->assertTrue($user->disabled);
});

// CT03
test('desabilita usuario com 46 dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(false, 46);

    $service->execute($user);

    $this->assertTrue($user->disabled);
});

// CT04
test('nao desabilita administrador com 45 dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(true, 45);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT05
test('nao desabilita administrador com 44 dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(true, 44);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT06
test('nao desabilita usuario com zero dias sem acesso', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(false, 0);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});

// CT07
test('nao desabilita usuario com dias negativos', function () {
    $service = ($this->createService)();
    $user = ($this->createUser)(false, -1);

    $service->execute($user);

    $this->assertFalse($user->disabled);
});
