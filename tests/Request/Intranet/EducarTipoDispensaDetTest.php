<?php

use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->type = LegacyExemptionTypeFactory::new()->create([
        'nm_tipo' => 'Atestado médico',
        'descricao' => 'Dispensa por motivo de saúde',
        'ref_cod_instituicao' => $this->institution,
    ]);

    $this->url = '/intranet/educar_tipo_dispensa_det.php?cod_tipo_dispensa=' . $this->type->getKey();
});

test('mostra os dados do tipo de dispensa, a instituição e o link de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Atestado médico')
        ->assertSee('Dispensa por motivo de saúde')
        ->assertSee('educar_tipo_dispensa_cad.php?cod_tipo_dispensa=' . $this->type->getKey(), false);
});

test('não oferece editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(577, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Atestado médico')
        ->assertDontSee('educar_tipo_dispensa_cad.php', false);
});

test('volta para a listagem quando o tipo de dispensa não existe', function () {
    $this->get('/intranet/educar_tipo_dispensa_det.php?cod_tipo_dispensa=0')->assertRedirect('educar_tipo_dispensa_lst.php');
});
