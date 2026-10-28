<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->religion = ReligionFactory::new()->create(['name' => 'Registro Alfa']);
    $this->url = '/intranet/educar_religiao_det.php?cod_religiao=' . $this->religion->getKey();
});

test('mostra o código e o nome da religião e os links de novo e de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Religião', (string) $this->religion->getKey(), 'Nome Religião', 'Registro Alfa'])
        ->assertSee('educar_religiao_cad.php?cod_religiao=' . $this->religion->getKey(), false)
        ->assertSee('educar_religiao_lst.php', false);
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(579, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_religiao_cad.php', false);
});

test('volta para a listagem avisando quando religião não existe', function () {
    $this->get('/intranet/educar_religiao_det.php?cod_religiao=0')
        ->assertRedirect('educar_religiao_lst.php')
        ->assertSessionHas('error', 'Religião não encontrada. O registro não existe ou foi excluído. Use a busca abaixo para localizá-la.');
});
