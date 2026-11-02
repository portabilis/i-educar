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

test('duplicate cod aluno', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'alunos' => collect([
            [
                'codAluno' => $this->studentOne->getKey(),
                'aluno_principal' => true,
            ],
            [
                'codAluno' => $this->studentOne->getKey(),
                'aluno_principal' => false,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_aluno.php', $payload)
        ->assertSuccessful()
        ->assertSee('Erro ao tentar unificar Alunos, foi inserido cadastro duplicados');
});
