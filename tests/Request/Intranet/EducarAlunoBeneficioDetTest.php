<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->benefit = fn (array $attributes = []) => LegacyBenefitFactory::new()->create($attributes + [
        'nm_beneficio' => 'Registro Alfa',
        'desc_beneficio' => "Primeira linha\nSegunda linha",
        'bolsa_familia' => true,
    ]);
    $this->url = fn ($benefit) => '/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $benefit->getKey();
});

test('mostra os dados do benefício e os links de novo e de edição', function () {
    $benefit = ($this->benefit)();

    $this->get(($this->url)($benefit))
        ->assertOk()
        ->assertSeeInOrder(['Código Benefício', (string) $benefit->getKey(), 'Benefício', 'Registro Alfa', 'Descrição', 'Bolsa Família', 'Sim'])
        ->assertSee("Primeira linha<br />\nSegunda linha", false)
        ->assertSee('educar_aluno_beneficio_cad.php?cod_aluno_beneficio=' . $benefit->getKey(), false)
        ->assertSee('educar_aluno_beneficio_lst.php', false);
});

test('omite a descrição vazia e mostra que não é Bolsa Família', function () {
    $benefit = ($this->benefit)(['desc_beneficio' => null, 'bolsa_familia' => false]);

    $this->get(($this->url)($benefit))
        ->assertOk()
        ->assertSeeInOrder(['Benefício', 'Registro Alfa', 'Bolsa Família', 'Não'])
        ->assertDontSee('Descrição');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $benefit = ($this->benefit)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(581, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->url)($benefit))
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_aluno_beneficio_cad.php', false);
});

test('volta para a listagem quando o benefício não existe', function () {
    $this->get('/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=0')->assertRedirect('educar_aluno_beneficio_lst.php');
});
