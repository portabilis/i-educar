<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('mostra o detalhe da turma com as etapas', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['nm_turma' => 'Turma Alfa']);

    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);

    $this->get('/intranet/educar_turma_det.php?cod_turma=' . $schoolClass->getKey())
        ->assertOk()
        ->assertSee('Turma - Detalhe')
        ->assertSee('Turma Alfa');
});

test('volta para a listagem quando a turma não é informada ou não existe', function () {
    $this->get('/intranet/educar_turma_det.php')->assertRedirect('educar_turma_lst.php');
    $this->get('/intranet/educar_turma_det.php?cod_turma=999999999')->assertRedirect('educar_turma_lst.php');
});
