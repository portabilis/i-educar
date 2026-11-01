<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa']);
    $this->link = fn ($course) => 'educar_curso_det.php?cod_curso=' . $course->getKey();
});

test('lista os cursos ativos com o link para o detalhe', function () {
    $inactive = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Inativo', 'ativo' => 0]);

    $this->get('/intranet/educar_curso_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->course), false)
        ->assertDontSee(($this->link)($inactive), false)
        ->assertSee('go("educar_curso_cad.php")', false);
});

test('filtra pelo nome', function () {
    $this->get('/intranet/educar_curso_lst.php?nm_curso=curso alf')->assertOk()->assertSee(($this->link)($this->course), false);
    $this->get('/intranet/educar_curso_lst.php?nm_curso=nome-que-nao-existe')->assertOk()->assertDontSee(($this->link)($this->course), false);
});

test('filtra pelo nível e pelo tipo de ensino', function () {
    $level = LegacyEducationLevelFactory::new()->create($this->institution);
    $type = LegacyEducationTypeFactory::new()->create($this->institution);

    $other = LegacyCourseFactory::new()->create($this->institution + [
        'nm_curso' => 'Curso Beta',
        'ref_cod_nivel_ensino' => $level,
        'ref_cod_tipo_ensino' => $type,
    ]);

    $this->get('/intranet/educar_curso_lst.php?ref_cod_nivel_ensino=' . $level->getKey())
        ->assertOk()
        ->assertSee(($this->link)($other), false)
        ->assertDontSee(($this->link)($this->course), false);

    $this->get('/intranet/educar_curso_lst.php?ref_cod_tipo_ensino=' . $type->getKey())
        ->assertOk()
        ->assertSee(($this->link)($other), false)
        ->assertDontSee(($this->link)($this->course), false);
});

test('não oferece o botão de novo curso a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(566, modify: false, remove: false)->create());

    $this->get('/intranet/educar_curso_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->course), false)
        ->assertDontSee('go("educar_curso_cad.php")', false);
});
