<?php

use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyProjectFactory::new()->create();
});

test('mostra o detalhe de projeto', function () {
    $this->get('/intranet/educar_projeto_det.php?cod_projeto=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando projeto não existe', function () {
    $this->get('/intranet/educar_projeto_det.php?cod_projeto=0')->assertRedirect();
});
