<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre a tela de atualização de matrículas', function () {
    $this->get('/module/Avaliacao/promocao')->assertOk();
});
