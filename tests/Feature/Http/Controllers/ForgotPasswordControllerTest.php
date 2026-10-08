<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function test_reset_link_request_without_login_returns_validation_error(): void
    {
        Notification::fake();

        $response = $this->from('/password/reset')->post('/password/email');

        $response->assertRedirect('/password/reset')
            ->assertSessionHasErrors([
                'login' => 'O campo matrícula é obrigatório.',
            ]);

        Notification::assertNothingSent();
    }

    public function test_reset_link_request_with_blank_login_returns_validation_error(): void
    {
        Notification::fake();

        $response = $this->from('/password/reset')->post('/password/email', ['login' => '   ']);

        $response->assertRedirect('/password/reset')
            ->assertSessionHasErrors([
                'login' => 'O campo matrícula é obrigatório.',
            ]);

        Notification::assertNothingSent();
    }
}
