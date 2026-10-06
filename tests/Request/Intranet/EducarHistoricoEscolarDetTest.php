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

test('mostra o detalhe do histórico escolar', function () {
    $this->get('/intranet/educar_historico_escolar_det.php?' . $this->keys)
        ->assertOk()
        ->assertSee((string) $this->history->ano);
});

test('volta para a listagem quando o histórico não existe', function () {
    $this->get('/intranet/educar_historico_escolar_det.php?ref_cod_aluno=' . $this->history->ref_cod_aluno . '&sequencial=99')
        ->assertRedirect();
});
