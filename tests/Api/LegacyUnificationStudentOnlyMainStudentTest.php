<?php

use App\Models\LogUnification;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

beforeEach(function () {
    $this->loginWithFirstUser();

    $this->student = LegacyStudentFactory::new()->create();
});

test('only main student', function () {
    $payload = [
        'tipoacao' => 'Novo',
        'alunos' => json_encode([
            [
                'codAluno' => $this->student->getKey(),
                'aluno_principal' => true,
            ],
        ]),
    ];

    $this->post('/intranet/educar_unifica_aluno.php', $payload)
        ->assertSuccessful()
        ->assertSee('Informe pelo menos dois alunos para unificar.');

    $this->assertDatabaseMissing(LogUnification::class, [
        'main_id' => $this->student->getKey(),
    ]);
});
