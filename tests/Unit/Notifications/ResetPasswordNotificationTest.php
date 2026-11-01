<?php

use App\Notifications\ResetPasswordNotification;
use Tests\TestCase;

uses(TestCase::class);

test('a notificação de senha alterada vai por e-mail com o link para trocar de novo', function () {
    $notification = new ResetPasswordNotification;
    $mail = $notification->toMail(new stdClass);

    expect($notification->via(null))->toBe(['mail'])
        ->and($notification->toArray(null))->toBe([])
        ->and($mail->subject)->toBe('Sua senha foi alterada')
        ->and($mail->actionText)->toBe('Alterar Senha')
        ->and($mail->actionUrl)->toBe(route('password.request'));
});
