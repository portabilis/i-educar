<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->type = LegacyTransferTypeFactory::new()->create([
        'nm_tipo' => 'Mudança de endereço',
        'desc_tipo' => 'A família mudou de bairro',
        'ref_cod_instituicao' => $this->institution,
    ]);

    $this->url = '/intranet/educar_transferencia_tipo_det.php?cod_transferencia_tipo=' . $this->type->getKey();
});

test('mostra os dados do tipo de transferência, a instituição e o link de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Mudança de endereço')
        ->assertSee('A família mudou de bairro')
        ->assertSee('educar_transferencia_tipo_cad.php?cod_transferencia_tipo=' . $this->type->getKey(), false);
});

test('não oferece editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(575, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Mudança de endereço')
        ->assertDontSee('educar_transferencia_tipo_cad.php', false);
});

test('volta para a listagem quando o tipo de transferência não existe', function () {
    $this->get('/intranet/educar_transferencia_tipo_det.php?cod_transferencia_tipo=0')->assertRedirect('educar_transferencia_tipo_lst.php');
});
