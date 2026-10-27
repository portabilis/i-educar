<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->type = LegacyDisciplinaryOccurrenceTypeFactory::new()->create([
        'name' => 'Atraso recorrente',
        'description' => 'Chegou depois do sinal',
        'max' => 7,
        'institution_id' => $this->institution,
    ]);

    $this->url = '/intranet/educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar=' . $this->type->getKey();
});

test('mostra os dados do tipo de ocorrência disciplinar, a instituição e o link de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Atraso recorrente')
        ->assertSee('Chegou depois do sinal')
        ->assertSee('7')
        ->assertSee('educar_tipo_ocorrencia_disciplinar_cad.php?cod_tipo_ocorrencia_disciplinar=' . $this->type->getKey(), false);
});

test('não oferece editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(580, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Atraso recorrente')
        ->assertDontSee('educar_tipo_ocorrencia_disciplinar_cad.php', false);
});

test('volta para a listagem quando o tipo de ocorrência disciplinar não existe', function () {
    $this->get('/intranet/educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar=0')->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
});
