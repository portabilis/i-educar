<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// O cadastro de usuários pelo site está desligado nas rotas; o controller
// segue existindo com as regras padrão do Laravel.
test('o cadastro valida nome, e-mail e senha', function () {
    $controller = new RegisterController;
    $validator = fn (array $data) => $this->validator($data);

    expect($validator->call($controller, [])->fails())->toBeTrue()
        ->and($validator->call($controller, ['name' => 'Ana', 'email' => 'ana@exemplo.test', 'password' => 'Senha@123', 'password_confirmation' => 'Senha@123'])->passes())->toBeTrue();
});

test('a verificação de e-mail exige autenticação e assinatura', function () {
    expect((new VerificationController)->getMiddleware())->toHaveCount(3);
});
