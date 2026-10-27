<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\RegionalTypeFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = RegionalTypeFactory::new()->create();
});

test('mostra o detalhe de regional', function () {
    $this->get('/intranet/educar_regional_det.php?id=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando regional não existe', function () {
    $this->get('/intranet/educar_regional_det.php?id=0')->assertRedirect();
});
