<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyBenefitFactory::new()->create();
});

test('lista benefício e filtra pelo nome', function () {
    $link = 'educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $this->record->getKey();

    $this->get('/intranet/educar_aluno_beneficio_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_aluno_beneficio_lst.php?nm_beneficio=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
