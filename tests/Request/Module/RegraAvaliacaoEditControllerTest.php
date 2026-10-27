<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->rule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Alfa']);
});

test('abre o formulário de uma nova regra de avaliação', function () {
    $this->get('/module/RegraAvaliacao/edit')->assertOk();
});

test('abre o formulário de edição da regra de avaliação', function () {
    $payload = LegacyFields::from($this->get('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey())->assertOk()->getContent());

    expect($payload['nome'])->toBe('Regra Alfa');
});
