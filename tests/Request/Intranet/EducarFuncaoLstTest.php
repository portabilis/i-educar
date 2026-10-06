<?php

use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRoleFactory::new()->create();
});

test('lista função e filtra pelo nome', function () {
    $link = 'educar_funcao_det.php?cod_funcao=' . $this->record->getKey();

    $this->get('/intranet/educar_funcao_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_funcao_lst.php?nm_funcao=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
