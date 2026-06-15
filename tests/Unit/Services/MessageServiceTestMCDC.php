<?php

use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Mockery as m;
use Mockery\MockInterface;

uses()->group('mcdc', 'message-service');

beforeEach(function () {});

afterEach(function () {
    m::close();
});

function callIsPoliOrInstitutionalUser(MessageService $svc, int $userId): bool
{
    $ref = new ReflectionClass($svc);
    $method = $ref->getMethod('isPoliOrInstitutionalUser');
    $method->setAccessible(true);

    return $method->invokeArgs($svc, [$userId]);
}

/**
 * Helper: mock de Auth::id() e Auth::user()->isAdmin()/isInstitutional()
 */
function mockAuthUser(int $authId, bool $isAdmin, bool $isInstitutional): void
{
    $authUser = m::mock(stdClass::class);
    $authUser->shouldReceive('isAdmin')->andReturn($isAdmin)->byDefault();
    $authUser->shouldReceive('isInstitutional')->andReturn($isInstitutional)->byDefault();

    Auth::shouldReceive('id')->andReturn($authId)->byDefault();
    Auth::shouldReceive('user')->andReturn($authUser)->byDefault();
}

/**
 * Helper: mocka a classe User::find(<id>) usada no service.
 * Usamos alias para "App\User" (classe importada no service original).
 * Se o projeto usar outra classe (ex.: App\Models\LegacyUser), ajuste o alias aqui.
 */
function userAlias(): MockInterface
{
    return m::mock('alias:App\User');
}

/**
 * Helper: cria um "user" stub com isAdmin()/isInstitutional()
 */
function stubUser(bool $isAdmin, bool $isInstitutional)
{
    $u = m::mock(stdClass::class);
    $u->shouldReceive('isAdmin')->andReturn($isAdmin)->byDefault();
    $u->shouldReceive('isInstitutional')->andReturn($isInstitutional)->byDefault();

    return $u;
}

/** CT1 — Admin logado: D1=V; D2(A=V,B=F) => true */
test('CT1 - Admin logado retorna true', function () {
    $svc = new MessageService;
    $id = 10;

    mockAuthUser($id, /* A */ true, /* B */ false);

    expect(callIsPoliOrInstitutionalUser($svc, $id))->toBeTrue();
});

/** CT2 — Institucional logado: D1=V; D2(A=F,B=V) => true */
test('CT2 - Institucional logado retorna true', function () {
    $svc = new MessageService;
    $id = 11;

    mockAuthUser($id, /* A */ false, /* B */ true);

    expect(callIsPoliOrInstitutionalUser($svc, $id))->toBeTrue();
});

/** CT3 — Logado sem privilégio: D1=V; D2(A=F,B=F) => false */
test('CT3 - Logado sem privilégio retorna false', function () {
    $svc = new MessageService;
    $id = 12;

    mockAuthUser($id, /* A */ false, /* B */ false);

    expect(callIsPoliOrInstitutionalUser($svc, $id))->toBeFalse();
});

/** CT4 — Usuário externo inexistente: D1=F; C=F => false */
test('CT4 - Usuário externo inexistente retorna false', function () {
    $svc = new MessageService;

    $authId = 100;
    $otherId = 200;

    mockAuthUser($authId, false, false); // garante D1 = F (otherId != authId)

    $alias = userAlias();
    $alias->shouldReceive('find')->with($otherId)->andReturn(null);

    expect(callIsPoliOrInstitutionalUser($svc, $otherId))->toBeFalse();
});

/** CT5 — Externo admin: D1=F; C=V,D=V,E=F => true */
test('CT5 - Usuário externo admin retorna true', function () {
    $svc = new MessageService;

    $authId = 100;
    $otherId = 201;

    mockAuthUser($authId, false, false);

    $alias = userAlias();
    $alias->shouldReceive('find')->with($otherId)->andReturn(stubUser(/* D */ true, /* E */ false));

    expect(callIsPoliOrInstitutionalUser($svc, $otherId))->toBeTrue();
});

/** CT6 — Externo institucional: D1=F; C=V,D=F,E=V => true */
test('CT6 - Usuário externo institucional retorna true', function () {
    $svc = new MessageService;

    $authId = 100;
    $otherId = 202;

    mockAuthUser($authId, false, false);

    $alias = userAlias();
    $alias->shouldReceive('find')->with($otherId)->andReturn(stubUser(/* D */ false, /* E */ true));

    expect(callIsPoliOrInstitutionalUser($svc, $otherId))->toBeTrue();
});

/** CT7 — Externo comum: D1=F; C=V,D=F,E=F => false */
test('CT7 - Usuário externo comum retorna false', function () {
    $svc = new MessageService;

    $authId = 100;
    $otherId = 203;

    mockAuthUser($authId, false, false);

    $alias = userAlias();
    $alias->shouldReceive('find')->with($otherId)->andReturn(stubUser(/* D */ false, /* E */ false));

    expect(callIsPoliOrInstitutionalUser($svc, $otherId))->toBeFalse();
});
