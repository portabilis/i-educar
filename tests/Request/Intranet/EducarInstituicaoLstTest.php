<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista as instituições e filtra pelo nome', function () {
    $this->get('/intranet/educar_instituicao_lst.php')
        ->assertOk()
        ->assertSee('educar_instituicao_det.php?cod_instituicao=' . $this->user->ref_cod_instituicao, false);

    $this->get('/intranet/educar_instituicao_lst.php?nm_instituicao=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee('educar_instituicao_det.php?cod_instituicao=' . $this->user->ref_cod_instituicao, false);
});
