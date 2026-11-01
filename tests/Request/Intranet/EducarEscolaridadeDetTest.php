<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->degree = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Registro Alfa']);
    $this->url = '/intranet/educar_escolaridade_det.php?idesco=' . $this->degree->getKey();
});

test('mostra a descrição da escolaridade e os links de novo e de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Descrição', 'Registro Alfa'])
        ->assertSee('educar_escolaridade_cad.php?idesco=' . $this->degree->getKey(), false)
        ->assertSee('educar_escolaridade_lst.php', false);
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(632, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_escolaridade_cad.php', false);
});

test('volta para a listagem quando a escolaridade não existe', function () {
    $this->get('/intranet/educar_escolaridade_det.php?idesco=0')->assertRedirect('educar_escolaridade_lst.php');
});
