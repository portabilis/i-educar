<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->course = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Alfa']);
});

test('abre a listagem de cursos em modo de visualização', function () {
    $this->get('/intranet/educar_curso_lst_v.php?nm_curso=Curso Alfa')
        ->assertOk()
        ->assertSee('Curso Alfa');
});
