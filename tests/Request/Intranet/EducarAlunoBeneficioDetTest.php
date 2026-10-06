<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyBenefitFactory::new()->create();
});

test('mostra o detalhe de benefício', function () {
    $this->get('/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando benefício não existe', function () {
    $this->get('/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=0')->assertRedirect();
});
