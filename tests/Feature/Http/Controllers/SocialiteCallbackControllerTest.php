<?php

use Tests\TestCase;

uses(TestCase::class);

test('callback with invalid state redirects to login', function () {
    $response = $this->withSession(['state' => 'expected-state'])
        ->get('/auth/callback?code=code&state=other-state');

    $response->assertRedirect('/login')
        ->assertSessionHasErrors([
            'login' => 'Sessão de login expirada. Tente entrar novamente.',
        ]);
});

test('callback without state in session redirects to login', function () {
    $response = $this->get('/auth/callback?code=code&state=reused-state');

    $response->assertRedirect('/login')
        ->assertSessionHasErrors('login');
});
