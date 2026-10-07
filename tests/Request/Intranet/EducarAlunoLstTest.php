<?php

use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista os alunos filtrando pelo código', function () {
    $student = LegacyStudentFactory::new()->create();
    $other = LegacyStudentFactory::new()->create();

    $this->get('/intranet/educar_aluno_lst.php?cod_aluno=' . $student->getKey())
        ->assertOk()
        ->assertSee($student->individual->person->nome)
        ->assertDontSee($other->individual->person->nome);
});

test('recusa uma data de nascimento inválida no filtro', function () {
    $this->get('/intranet/educar_aluno_lst.php?data_nascimento=31/02/1800')
        ->assertOk()
        ->assertSee('Aluno - Listagem');
});
