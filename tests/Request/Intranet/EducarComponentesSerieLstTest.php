<?php

use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Série Alfa']);
});

test('lista as séries que têm componentes vinculados', function () {
    LegacyDisciplineAcademicYearFactory::new()->create(['ano_escolar_id' => $this->grade->getKey()]);

    $query = http_build_query([
        'busca' => 'S',
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_curso' => $this->grade->ref_cod_curso,
        'ref_cod_serie' => $this->grade->getKey(),
    ]);

    $this->get('/intranet/educar_componentes_serie_lst.php?' . $query)
        ->assertOk()
        ->assertSee('educar_componentes_serie_cad.php?serie_id=' . $this->grade->getKey(), false);
});
