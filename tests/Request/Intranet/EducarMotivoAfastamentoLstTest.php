<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = WithdrawalReasonFactory::new()->create();
});

test('lista motivo de afastamento e filtra pelo nome', function () {
    $link = 'educar_motivo_afastamento_det.php?cod_motivo_afastamento=' . $this->record->getKey();

    $this->get('/intranet/educar_motivo_afastamento_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_motivo_afastamento_lst.php?nm_motivo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
