<?php

use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyTransferTypeFactory::new()->create();
});

test('lista tipo de transferência e filtra pelo nome', function () {
    $link = 'educar_transferencia_tipo_det.php?cod_transferencia_tipo=' . $this->record->getKey();

    $this->get('/intranet/educar_transferencia_tipo_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_transferencia_tipo_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
