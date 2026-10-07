<?php

use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyProjectFactory::new()->create();
});

test('cadastra projeto', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_projeto_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nome' => 'Registro Alfa',
        'observacao' => 'Observação do projeto',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_projeto_cad.php', $payload)->assertRedirect();
});

test('edita e exclui projeto', function () {
    $url = '/intranet/educar_projeto_cad.php?cod_projeto=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
