<?php

use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

uses(TestCase::class);

test('reset link request without login returns validation error', function () {
    Notification::fake();

    $response = $this->from('/password/reset')->post('/password/email');

    $response->assertRedirect('/password/reset')
        ->assertSessionHasErrors([
            'login' => 'O campo matrícula é obrigatório.',
        ]);

    Notification::assertNothingSent();
});

test('reset link request with blank login returns validation error', function () {
    Notification::fake();

    $response = $this->from('/password/reset')->post('/password/email', ['login' => '   ']);

    $response->assertRedirect('/password/reset')
        ->assertSessionHasErrors([
            'login' => 'O campo matrícula é obrigatório.',
        ]);

    Notification::assertNothingSent();
});
