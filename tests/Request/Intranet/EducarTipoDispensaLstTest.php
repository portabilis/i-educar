<?php

use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyExemptionTypeFactory::new()->create();
});

test('lista tipo de dispensa e filtra pelo nome', function () {
    $link = 'educar_tipo_dispensa_det.php?cod_tipo_dispensa=' . $this->record->getKey();

    $this->get('/intranet/educar_tipo_dispensa_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_tipo_dispensa_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
