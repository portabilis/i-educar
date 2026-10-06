<?php

use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationLevelFactory::new()->create();
});

test('cadastra nível de ensino', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_nivel_ensino_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_nivel' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_nivel_ensino_cad.php', $payload)->assertRedirect();
});

test('edita e exclui nível de ensino', function () {
    $url = '/intranet/educar_nivel_ensino_cad.php?cod_nivel_ensino=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_nivel' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
