<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->distribution = UniformDistributionFactory::new()->create();
    $this->keys = 'ref_cod_aluno=' . $this->distribution->student_id . '&cod_distribuicao_uniforme=' . $this->distribution->getKey();
});

test('abre o formulário de uma nova distribuição', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_cad.php?ref_cod_aluno=' . $this->distribution->student_id)->assertOk();
});

test('edita a distribuição de uniforme', function () {
    $url = '/intranet/educar_distribuicao_uniforme_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['socks_qty' => 7]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->distribution->refresh()->socks_qty)->toBe(7);
});

test('exclui a distribuição de uniforme', function () {
    $url = '/intranet/educar_distribuicao_uniforme_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();
});
