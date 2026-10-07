<?php

use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyDeficiencyFactory::new()->create();
});

test('mostra o detalhe de deficiência', function () {
    $this->get('/intranet/educar_deficiencia_det.php?cod_deficiencia=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando deficiência não existe', function () {
    $this->get('/intranet/educar_deficiencia_det.php?cod_deficiencia=0')->assertRedirect();
});
