<?php

use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationTypeFactory::new()->create();
});

test('cadastra tipo de ensino', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_tipo_ensino_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_tipo' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_tipo_ensino_cad.php', $payload)->assertRedirect();
});

test('edita e exclui tipo de ensino', function () {
    $url = '/intranet/educar_tipo_ensino_cad.php?cod_tipo_ensino=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_tipo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});

test('volta para a listagem avisando quando o tipo de ensino não existe', function () {
    $this->get('/intranet/educar_tipo_ensino_cad.php?cod_tipo_ensino=0')
        ->assertRedirect('educar_tipo_ensino_lst.php')
        ->assertSessionHas('error', 'Tipo de ensino não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.');
});
