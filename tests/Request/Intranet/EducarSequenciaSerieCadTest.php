<?php

use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->sequence = LegacyGradeSequenceFactory::new()->create(['ref_usuario_cad' => $this->user->getKey()]);
});

test('abre o formulário de uma nova sequência', function () {
    $this->get('/intranet/educar_sequencia_serie_cad.php')->assertOk();
});

test('edita e exclui a sequência de enturmação', function () {
    $url = '/intranet/educar_sequencia_serie_cad.php?id=' . $this->sequence->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
