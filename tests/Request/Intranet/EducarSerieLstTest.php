<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa']);
    $this->grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Série Alfa']);

    $this->link = fn ($grade) => 'educar_serie_det.php?cod_serie=' . $grade->getKey();
});

test('lista as séries ativas com a coluna de instituição para o administrador', function () {
    $inactive = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Série Inativa', 'ativo' => 0]);

    $this->get('/intranet/educar_serie_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->grade), false)
        ->assertDontSee(($this->link)($inactive), false)
        ->assertSee($this->course->institution->nm_instituicao)
        ->assertSee('go("educar_serie_cad.php")', false);
});

test('filtra pelo nome e pelo curso', function () {
    $otherCourse = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Beta']);
    $other = LegacyGradeFactory::new()->create(['ref_cod_curso' => $otherCourse, 'nm_serie' => 'Série Beta']);

    $this->get('/intranet/educar_serie_lst.php?nm_serie=série alf')
        ->assertOk()
        ->assertSee(($this->link)($this->grade), false)
        ->assertDontSee(($this->link)($other), false);

    $this->get('/intranet/educar_serie_lst.php?ref_cod_instituicao=' . $this->user->ref_cod_instituicao . '&ref_cod_curso=' . $otherCourse->getKey())
        ->assertOk()
        ->assertSee(($this->link)($other), false)
        ->assertDontSee(($this->link)($this->grade), false);
});

test('o usuário institucional só vê as séries da própria instituição', function () {
    $foreignCourse = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]);
    $foreign = LegacyGradeFactory::new()->create(['ref_cod_curso' => $foreignCourse, 'nm_serie' => 'Série de Fora']);

    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(583, modify: false, remove: false)->create());

    $response = $this->get('/intranet/educar_serie_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->grade), false)
        ->assertDontSee(($this->link)($foreign), false)
        ->assertDontSee('go("educar_serie_cad.php")', false);

    // Sem a lista de instituições: só o administrador escolhe a instituição.
    expect(LegacyForm::from($response->getContent())->options('ref_cod_instituicao'))->toBe([]);
});
