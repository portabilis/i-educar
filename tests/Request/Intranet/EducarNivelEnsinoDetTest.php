<?php

use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationLevelFactory::new()->create();
});

test('mostra o detalhe de nível de ensino', function () {
    $this->get('/intranet/educar_nivel_ensino_det.php?cod_nivel_ensino=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando nível de ensino não existe', function () {
    $this->get('/intranet/educar_nivel_ensino_det.php?cod_nivel_ensino=0')->assertRedirect();
});
