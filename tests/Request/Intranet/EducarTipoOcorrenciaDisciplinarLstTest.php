<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyDisciplinaryOccurrenceTypeFactory::new()->create();
});

test('lista tipo de ocorrência disciplinar e filtra pelo nome', function () {
    $link = 'educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar=' . $this->record->getKey();

    $this->get('/intranet/educar_tipo_ocorrencia_disciplinar_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_tipo_ocorrencia_disciplinar_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
