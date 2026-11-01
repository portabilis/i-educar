<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->student = fn (string $name, ?string $socialName = null) => LegacyStudentFactory::new()->create([
        'ref_idpes' => LegacyIndividualFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F']),
            'nome_social' => $socialName,
        ]),
    ]);

    $this->search = fn (array $query) => $this->get('/intranet/educar_pesquisa_aluno.php?' . http_build_query($query))->assertOk();
});

test('lista os alunos pelo nome, mostrando o nome social com o nome de registro', function () {
    $ana = ($this->student)('Ana Alves', 'Aninha');
    ($this->student)('Bia Borges');

    ($this->search)(['nm_aluno' => 'ana'])
        ->assertSee('Aninha - Nome de registro: Ana Alves')
        ->assertSee("addVal1('ref_cod_aluno','{$ana->getKey()}')", false)
        ->assertDontSee('Bia Borges');
});

test('filtra pelo código e pela escola da última matrícula', function () {
    $ana = ($this->student)('Ana Alves');
    $bia = ($this->student)('Bia Borges');
    $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $bia, 'ultima_matricula' => 1]);

    ($this->search)(['cod_aluno' => $ana->getKey()])
        ->assertSee('Ana Alves')
        ->assertDontSee('Bia Borges');

    ($this->search)(['ref_cod_escola' => $registration->ref_ref_cod_escola])
        ->assertSee('Bia Borges')
        ->assertDontSee('Ana Alves');
});
