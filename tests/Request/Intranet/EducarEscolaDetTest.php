<?php

use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('mostra o detalhe da escola com cursos e anos letivos', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);

    LegacySchoolCourseFactory::new()->create(['ref_cod_escola' => $school]);
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school]);

    $this->get('/intranet/educar_escola_det.php?cod_escola=' . $school->getKey())
        ->assertOk()
        ->assertSee('Escola - Detalhe')
        ->assertSee('Escola Alfa');
});

test('sai do detalhe quando a escola não existe', function () {
    $this->get('/intranet/educar_escola_det.php?cod_escola=0')
        ->assertRedirect('educar_escola_lst.php')
        ->assertSessionHas('error', 'Escola não encontrada. O registro não existe ou foi excluído. Use a busca abaixo para localizá-la.');
});
