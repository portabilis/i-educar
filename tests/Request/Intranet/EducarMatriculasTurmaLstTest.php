<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
});

test('lista as turmas para enturmação em lote', function () {
    $query = http_build_query([
        'busca' => 'S',
        'ano' => $this->schoolClass->ano,
        'ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao,
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $this->schoolClass->ref_cod_curso,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
    ]);

    $this->get('/intranet/educar_matriculas_turma_lst.php?' . $query)
        ->assertOk()
        ->assertSee('Turma Alfa');
});
