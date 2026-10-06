<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->course = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Alfa']);
});

test('abre o formulário de um novo curso', function () {
    $this->get('/intranet/educar_curso_cad.php')->assertOk();
});

test('edita e exclui o curso', function () {
    $url = '/intranet/educar_curso_cad.php?cod_curso=' . $this->course->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_curso' => 'Curso renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->course->refresh()->nm_curso)->toBe('Curso renomeado');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
