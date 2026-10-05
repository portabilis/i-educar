<?php

use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRoleFactory::new()->create();
});

test('cadastra função', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_funcao_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_funcao' => 'Registro Alfa',
        'abreviatura' => 'RA',
        'professor' => 0,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_funcao_cad.php', $payload)->assertRedirect();
});

test('edita e exclui função', function () {
    $url = '/intranet/educar_funcao_cad.php?cod_funcao=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_funcao' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
