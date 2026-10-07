<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyDisciplinaryOccurrenceTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de ocorrência disciplinar', function () {
    $this->get('/intranet/educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando tipo de ocorrência disciplinar não existe', function () {
    $this->get('/intranet/educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar=0')->assertRedirect();
});
