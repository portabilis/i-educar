<?php

use App\Http\Middleware\CheckResetPassword;
use App\Http\Middleware\CheckToken;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;

uses(TestCase::class);

test('sem usuário logado a troca de senha obrigatória não interfere', function () {
    expect(app(CheckResetPassword::class)->handle(Request::create('/'), fn () => 'seguiu'))->toBe('seguiu');
});

test('token de API diferente do configurado é recusado', function () {
    config(['legacy.apis.access_key' => 'chave-certa']);
    $request = Request::create('/');
    $request->headers->set('Authorization', 'Bearer chave-errada');

    expect(fn () => (new CheckToken)->handle($request, fn () => 'seguiu'))->toThrow(UnauthorizedException::class);
});
