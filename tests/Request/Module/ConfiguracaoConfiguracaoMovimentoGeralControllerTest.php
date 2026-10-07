<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre e grava a configuração do movimento geral', function () {
    $payload = LegacyFields::from($this->get('/module/Configuracao/ConfiguracaoMovimentoGeral')->assertOk()->getContent());

    $this->post('/module/Configuracao/ConfiguracaoMovimentoGeral', $payload)->assertOk();
});
