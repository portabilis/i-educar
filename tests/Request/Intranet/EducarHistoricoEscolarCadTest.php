<?php

use Database\Factories\LegacySchoolHistoryDisciplineFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

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

test('abre o formulário de um novo histórico', function () {
    $this->get('/intranet/educar_historico_escolar_cad.php?ref_cod_aluno=' . $this->history->ref_cod_aluno)->assertOk();
});

test('abre o formulário de edição com as disciplinas do histórico', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_historico_escolar_cad.php?' . $this->keys)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');
});

test('exclui o histórico escolar', function () {
    $url = '/intranet/educar_historico_escolar_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();
});
