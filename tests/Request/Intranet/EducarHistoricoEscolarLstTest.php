<?php

use Database\Factories\LegacySchoolHistoryDisciplineFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->history = LegacySchoolHistoryFactory::new()->create();
    LegacySchoolHistoryDisciplineFactory::new()->create([
        'ref_ref_cod_aluno' => $this->history->ref_cod_aluno,
        'ref_sequencial' => $this->history->sequencial,
    ]);
    $this->keys = 'ref_cod_aluno=' . $this->history->ref_cod_aluno . '&sequencial=' . $this->history->sequencial;
});

test('lista os históricos escolares do aluno', function () {
    $this->get('/intranet/educar_historico_escolar_lst.php?ref_cod_aluno=' . $this->history->ref_cod_aluno)
        ->assertOk()
        ->assertSee('educar_historico_escolar_det.php?ref_cod_aluno=' . $this->history->ref_cod_aluno, false);
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_historico_escolar_lst.php')->assertRedirect();
});
