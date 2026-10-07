<?php

use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista as escolas e filtra pelo nome', function () {
    $institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    LegacySchoolFactory::new()->withName('Escola Alfa')->create($institution);
    LegacySchoolFactory::new()->withName('Escola Beta')->create($institution);

    $this->get('/intranet/educar_escola_lst.php?nm_escola=Escola Alfa&ref_cod_instituicao=' . $this->user->ref_cod_instituicao)
        ->assertOk()
        ->assertSee('Escola Alfa')
        ->assertDontSee('Escola Beta');
});
