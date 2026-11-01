<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre a tela de processamento de históricos', function () {
    $this->get('/module/HistoricoEscolar/processamento')->assertOk();
});
