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

test('not aluno principal', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'alunos' => collect([
            [
                'codAluno' => $this->studentOne->getKey(),
                'aluno_principal' => false,
            ],
            [
                'codAluno' => $this->studentTwo->getKey(),
                'aluno_principal' => false,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_aluno.php', $payload)
        ->assertSuccessful()
        ->assertSee('Aluno principal não informado');
});
