<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    Portabilis_Utils_User::$_currentUserId = null;
    Portabilis_Utils_User::$_nivelAcesso = null;
    Portabilis_Utils_User::$_permissoes = null;

    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('mostra o detalhe da matrícula enturmada', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $registration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create();

    $this->get('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey())
        ->assertOk()
        ->assertSee('Matrícula - Detalhe')
        ->assertSee('Turma Alfa');
});

test('sai do detalhe quando a matrícula não existe', function () {
    $this->get('/intranet/educar_matricula_det.php?cod_matricula=0')
        ->assertRedirect();
});
