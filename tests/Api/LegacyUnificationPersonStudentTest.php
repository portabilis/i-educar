<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

beforeEach(function () {
    $this->loginWithFirstUser();

    $this->individual = LegacyIndividualFactory::new()->create();
    $this->student = LegacyStudentFactory::new()->create();
    $this->studentTwo = LegacyStudentFactory::new()->create();
});

test('unification person student', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => collect([
            [
                'idpes' => $this->individual->getKey(),
                'pessoa_principal' => true,
            ],
            [
                'idpes' => $this->student->individual->getKey(),
                'pessoa_principal' => false,
            ],
            [
                'idpes' => $this->studentTwo->individual->getKey(),
                'pessoa_principal' => false,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Não é permitido unificar mais de uma pessoa vinculada com alunos. Efetue primeiro a unificação de alunos e tente novamente.');
});
