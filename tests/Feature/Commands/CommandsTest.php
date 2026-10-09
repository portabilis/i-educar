<?php

use Tests\TestCase;

uses(TestCase::class);

test('console command inspire', function () {
    $this->artisan('inspire')->assertExitCode(0);
});

test('console command admin password', function () {
    $this->artisan('admin:password 12345678')->assertExitCode(0);
});

test('console command disable users with access expired', function () {
    $this->artisan('disable:users-with-access-expired')->assertExitCode(0);
});

test('console command legacy create tests', function () {
    // Os testes gerados ficam em tests/Pages/Intranet, fora do Git: o que falta é
    // criado, e rodar de novo encontra os arquivos que já existem.
    @unlink(base_path('tests/Pages/Intranet/TopicIndexTest.php'));

    $this->artisan('legacy:create:tests')
        ->expectsOutput('Teste TopicIndexTest criado para a rota /intranet/topic_index.php')
        ->assertExitCode(0);
    $this->artisan('legacy:create:tests')
        ->expectsOutput('Teste TopicIndexTest criado para a rota /intranet/topic_index.php já criado ou houve falha')
        ->assertExitCode(0);
});

test('console command notifications delete', function () {
    $this->artisan('notifications:delete')->assertExitCode(0);
});

test('console command reset password', function () {
    $this->artisan('reset:password 12345678')->assertExitCode(0);
});

test('console command update school class period', function () {
    $this->artisan('update:school-class-period')->assertExitCode(0);
});
