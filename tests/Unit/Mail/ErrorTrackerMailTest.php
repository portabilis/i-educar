<?php

use App\Mail\ErrorTrackerMail;
use Tests\TestCase;

uses(TestCase::class);

test('o e-mail de erro vai para o destinatário com o assunto e o conteúdo informados', function () {
    $mail = new ErrorTrackerMail('suporte@exemplo.test', 'Erro no i-Educar', 'Detalhes do erro');

    $mail->assertTo('suporte@exemplo.test');
    $mail->assertHasSubject('Erro no i-Educar');

    expect($mail->build()->view)->toBe('errors.email')
        ->and($mail->viewData)->toBe(['content' => 'Detalhes do erro']);
});
