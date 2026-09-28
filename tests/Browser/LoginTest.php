<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Browser\Scenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

it('autentica com matrícula e senha válidas', function () {
    Scenario::enableSystem();
    Scenario::createAdmin(login: '987654', password: 'senha-do-teste', name: 'Ana Diretora');

    visit('/login')
        ->assertSee('Entrar')
        ->fill('login', '987654')
        ->fill('password', 'senha-do-teste')
        ->click('Entrar')
        // O i-Educar manda o usuário autenticado para a intranet, não para "/".
        ->assertPathIs('/intranet/educar_index.php')
        ->assertSee('Ana Diretora')
        ->assertNoJavaScriptErrors();
});
