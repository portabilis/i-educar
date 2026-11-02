<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->race = LegacyRaceFactory::new()->create(['nm_raca' => 'Registro Alfa']);
    $this->url = '/intranet/educar_raca_det.php?cod_raca=' . $this->race->getKey();
});

test('mostra o nome da raça e os links de novo e de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Raça', 'Registro Alfa'])
        ->assertSee('educar_raca_cad.php?cod_raca=' . $this->race->getKey(), false)
        ->assertSee('educar_raca_lst.php', false);
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(678, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_raca_cad.php', false);
});

test('volta para a listagem quando a raça não existe', function () {
    $this->get('/intranet/educar_raca_det.php?cod_raca=0')->assertRedirect('educar_raca_lst.php');
});
