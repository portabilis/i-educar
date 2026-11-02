<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->rule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Alfa']);
});

test('mostra o detalhe da regra de avaliação', function () {
    $this->get('/module/RegraAvaliacao/view?id=' . $this->rule->getKey())
        ->assertOk()
        ->assertSee('Regra Alfa');
});
