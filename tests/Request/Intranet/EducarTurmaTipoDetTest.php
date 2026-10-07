<?php

use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolClassTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de turma', function () {
    $this->get('/intranet/educar_turma_tipo_det.php?cod_turma_tipo=' . $this->record->getKey())->assertOk();
});
