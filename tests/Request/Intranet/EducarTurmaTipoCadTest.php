<?php

use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolClassTypeFactory::new()->create();
});

test('cadastra tipo de turma', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_turma_tipo_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_tipo' => 'Registro Alfa',
        'sgl_tipo' => 'RA',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_turma_tipo_cad.php', $payload)->assertRedirect();
});

test('edita e exclui tipo de turma', function () {
    $url = '/intranet/educar_turma_tipo_cad.php?cod_turma_tipo=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_tipo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
