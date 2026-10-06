<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyBenefitFactory::new()->create();
});

test('cadastra benefício', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_aluno_beneficio_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_beneficio' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_aluno_beneficio_cad.php', $payload)->assertRedirect();
});

test('edita e exclui benefício', function () {
    $url = '/intranet/educar_aluno_beneficio_cad.php?cod_aluno_beneficio=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_beneficio' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
