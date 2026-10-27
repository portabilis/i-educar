<?php

use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

beforeEach(function () {
    $this->loginWithFirstUser();

    $this->studentOne = LegacyStudentFactory::new()->create();
    $this->studentTwo = LegacyStudentFactory::new()->create();
});

test('json invalid', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'alunos' => [
            [
                'codAluno' => $this->studentOne->getKey(),
                'aluno_principal' => true,
            ],
            [
                'codAluno' => $this->studentTwo->getKey(),
                'aluno_principal' => false,
            ],
        ],
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_aluno.php', $payload)
        ->assertSuccessful()
        ->assertSee('Informações inválidas para unificação');
});
