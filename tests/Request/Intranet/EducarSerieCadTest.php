<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Série Alfa']);
});

test('abre o formulário de uma nova série', function () {
    $this->get('/intranet/educar_serie_cad.php')->assertOk();
});

test('edita e exclui a série', function () {
    $this->grade->evaluationRules()->attach(LegacyEvaluationRuleFactory::new()->create()->getKey(), ['ano_letivo' => now()->year]);

    $url = '/intranet/educar_serie_cad.php?cod_serie=' . $this->grade->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_serie' => 'Série renomeada']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->grade->refresh()->nm_serie)->toBe('Série renomeada');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
