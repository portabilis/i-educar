<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->type = LegacyAbandonmentTypeFactory::new()->create([
        'nome' => 'Mudança de cidade',
        'ref_cod_instituicao' => $this->institution,
    ]);

    $this->url = '/intranet/educar_abandono_tipo_det.php?cod_abandono_tipo=' . $this->type->getKey();
});

test('mostra os dados do motivo de abandono, a instituição e o link de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Mudança de cidade')
        ->assertSee('educar_abandono_tipo_cad.php?cod_abandono_tipo=' . $this->type->getKey(), false);
});

test('não oferece editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(950, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Mudança de cidade')
        ->assertDontSee('educar_abandono_tipo_cad.php', false);
});

test('volta para a listagem quando o motivo de abandono não existe', function () {
    $this->get('/intranet/educar_abandono_tipo_det.php?cod_abandono_tipo=0')->assertRedirect('educar_abandono_tipo_lst.php');
});
