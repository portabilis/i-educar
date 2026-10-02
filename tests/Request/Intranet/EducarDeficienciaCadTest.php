<?php

use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyDeficiencyFactory::new()->create();
});

test('cadastra deficiência', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_deficiencia_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_deficiencia' => 'Registro Alfa',
        'deficiency_type_id' => 1,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_deficiencia_cad.php', $payload)->assertRedirect();
});

test('edita e exclui deficiência', function () {
    $url = '/intranet/educar_deficiencia_cad.php?cod_deficiencia=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_deficiencia' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
