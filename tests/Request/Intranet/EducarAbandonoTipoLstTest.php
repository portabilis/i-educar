<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista os motivos de abandono e filtra pelo nome', function () {
    $institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    LegacyAbandonmentTypeFactory::new()->create(['nome' => 'Motivo Alfa'] + $institution);
    LegacyAbandonmentTypeFactory::new()->create(['nome' => 'Motivo Beta'] + $institution);

    $this->get('/intranet/educar_abandono_tipo_lst.php')
        ->assertOk()
        ->assertSee('Motivo Alfa')
        ->assertSee('Motivo Beta');

    $this->get('/intranet/educar_abandono_tipo_lst.php?nome=Alfa')
        ->assertOk()
        ->assertSee('Motivo Alfa')
        ->assertDontSee('Motivo Beta');
});
