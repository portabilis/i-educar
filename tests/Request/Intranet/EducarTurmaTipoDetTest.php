<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->type = LegacySchoolClassTypeFactory::new()->create([
        'nm_tipo' => 'Tempo Integral',
        'sgl_tipo' => 'INTG',
        'ref_cod_instituicao' => $this->institution,
    ]);

    $this->url = '/intranet/educar_turma_tipo_det.php?cod_turma_tipo=' . $this->type->getKey();
});

test('mostra os dados do tipo de turma, a instituição e o link de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Tempo Integral')
        ->assertSee('INTG')
        ->assertSee('educar_turma_tipo_cad.php?cod_turma_tipo=' . $this->type->getKey(), false);
});

test('não oferece editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(570, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Tempo Integral')
        ->assertDontSee('educar_turma_tipo_cad.php', false);
});

test('volta para a listagem avisando quando tipo de turma não existe', function () {
    $this->get('/intranet/educar_turma_tipo_det.php?cod_turma_tipo=0')
        ->assertRedirect('educar_turma_tipo_lst.php')
        ->assertSessionHas('error', 'Tipo de turma não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.');
});
