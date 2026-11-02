<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->rule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Alfa']);
});

test('lista as regras de avaliação', function () {
    $this->get('/module/RegraAvaliacao/index')
        ->assertOk()
        ->assertSee('Regra Alfa');
});
