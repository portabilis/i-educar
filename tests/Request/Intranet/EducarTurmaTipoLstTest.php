<?php

use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolClassTypeFactory::new()->create();
});

test('lista tipo de turma e filtra pelo nome', function () {
    $link = 'educar_turma_tipo_det.php?cod_turma_tipo=' . $this->record->getKey();

    $this->get('/intranet/educar_turma_tipo_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_turma_tipo_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
