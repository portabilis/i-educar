<?php

use Tests\Browser\Support\Scenario;

it('autentica com matrícula e senha válidas', function () {
    Scenario::install(adminLogin: 'adm-login');

    visit('/login')
        ->assertSee('Entrar')
        ->fill('login', 'adm-login')
        ->fill('password', Scenario::PASSWORD)
        ->click('Entrar')
        // O i-Educar manda o usuário autenticado para a intranet, não para "/".
        ->assertPathIs('/intranet/educar_index.php')
        ->assertSee('Ana Diretora')
        ->assertNoJavaScriptErrors();
});

it('recusa senha inválida e mantém o usuário no login', function () {
    Scenario::install(adminLogin: 'adm-login');

    visit('/login')
        ->fill('login', 'adm-login')
        ->fill('password', 'senha-errada')
        ->click('Entrar')
        ->assertPathIs('/login');
});
