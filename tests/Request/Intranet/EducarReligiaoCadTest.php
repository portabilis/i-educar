<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = ReligionFactory::new()->create();
});

test('cadastra religião', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_religiao_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_religiao' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_religiao_cad.php', $payload)->assertRedirect();
});

test('edita e exclui religião', function () {
    $url = '/intranet/educar_religiao_cad.php?cod_religiao=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_religiao' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
