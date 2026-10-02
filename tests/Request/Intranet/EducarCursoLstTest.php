<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->course = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Alfa']);
});

test('lista os cursos e filtra pelo nome', function () {
    $link = 'educar_curso_det.php?cod_curso=' . $this->course->getKey();

    $this->get('/intranet/educar_curso_lst.php?nm_curso=Curso Alfa')->assertOk()->assertSee($link, false);
    $this->get('/intranet/educar_curso_lst.php?nm_curso=nome-que-nao-existe')->assertOk()->assertDontSee($link, false);
});
