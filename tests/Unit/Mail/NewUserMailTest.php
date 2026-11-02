<?php

use App\Mail\NewUserMail;
use Tests\TestCase;

uses(TestCase::class);

test('o e-mail de boas-vindas usa a view de novo usuário', function () {
    expect((new NewUserMail('ana@exemplo.test', 'ana', 'segredo', 'Ana'))->build()->view)->toBe('mails.new-user');
});
