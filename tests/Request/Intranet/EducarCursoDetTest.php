<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->course = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Alfa']);
});

test('mostra o detalhe do curso', function () {
    $this->get('/intranet/educar_curso_det.php?cod_curso=' . $this->course->getKey())
        ->assertOk()
        ->assertSee('Curso Alfa');
});

test('volta para a listagem quando o curso não existe', function () {
    $this->get('/intranet/educar_curso_det.php?cod_curso=0')->assertRedirect();
});
