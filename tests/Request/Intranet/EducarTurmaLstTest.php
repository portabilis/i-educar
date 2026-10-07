<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista as turmas do ano filtrando pelo nome', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Beta']);

    $query = http_build_query([
        'busca' => 'S',
        'ano' => $schoolClass->ano,
        'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        'nm_turma' => 'Turma Alfa',
    ]);

    $this->get('/intranet/educar_turma_lst.php?' . $query)
        ->assertOk()
        ->assertSee('Turma Alfa')
        ->assertDontSee('Turma Beta');
});
