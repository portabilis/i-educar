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

// Defeito conhecido: a tela lê os atributos do registro antes de conferir se
// ele existe; um código inexistente dá erro 500 em vez de voltar para a
// listagem, como fazem as outras telas de tipo.
test('defeito: estoura quando o tipo de turma não existe', function () {
    $this->get('/intranet/educar_turma_tipo_det.php?cod_turma_tipo=0')->assertServerError();
});
