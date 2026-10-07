<?php

use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolingDegreeFactory::new()->create();
});

test('cadastra escolaridade', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_escolaridade_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'descricao' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_escolaridade_cad.php', $payload)->assertRedirect();
});

test('edita e exclui escolaridade', function () {
    $url = '/intranet/educar_escolaridade_cad.php?idesco=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['descricao' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
