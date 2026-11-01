<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

/**
 * A tela que executa as ações da tabela de anos letivos do detalhe da escola.
 * Ela não tem formulário próprio: recebe por POST a escola, o ano e a ação.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);

    $this->year = fn (int $andamento, array $attributes = []) => LegacySchoolAcademicYearFactory::new()->create($attributes + [
        'ref_cod_escola' => $this->school,
        'ano' => 2030,
        'andamento' => $andamento,
    ]);

    $this->act = fn (string $action, int $year = 2030) => $this->post('/intranet/educar_iniciar_ano_letivo.php', [
        'ref_cod_escola' => $this->school->getKey(),
        'ano' => $year,
        'tipo_acao' => $action,
    ]);

    $this->detail = "educar_escola_det.php?cod_escola={$this->school->getKey()}";
});

test('volta para a listagem de escolas sem os três dados da ação', function () {
    $this->get('/intranet/educar_iniciar_ano_letivo.php')->assertRedirect('educar_escola_lst.php');

    $this->post('/intranet/educar_iniciar_ano_letivo.php', ['ref_cod_escola' => $this->school->getKey(), 'ano' => 2030])
        ->assertRedirect('educar_escola_lst.php');
});

test('volta para a listagem de escolas quando a escola não tem o ano letivo', function () {
    ($this->act)('iniciar')->assertRedirect('educar_escola_lst.php');
});

test('encaminha a edição para a tela das etapas do ano letivo', function () {
    ($this->act)('editar')->assertRedirect(
        "educar_ano_letivo_modulo_cad.php?ref_cod_escola={$this->school->getKey()}&ano=2030&referrer=educar_escola_det.php"
    );
});

test('inicia um ano letivo ainda não iniciado', function () {
    $year = ($this->year)(0);

    ($this->act)('iniciar')
        ->assertOk()
        ->assertSee('Ano letivo inicializado com sucesso!', false)
        ->assertSee("window.location = '{$this->detail}#ano_letivo'", false);

    expect($year->refresh())
        ->andamento->toBe(1)
        ->ref_usuario_exc->toBe($this->user->getKey());
});

test('reabre um ano letivo finalizado', function () {
    $year = ($this->year)(2);

    ($this->act)('reabrir')->assertOk()->assertSee('Ano letivo inicializado com sucesso!', false);

    expect($year->refresh()->andamento)->toBe(1);
});

test('finaliza um ano letivo em andamento sem matrículas em andamento', function () {
    $year = ($this->year)(1);

    ($this->act)('finalizar')
        ->assertOk()
        ->assertSee('Ano letivo finalizado com sucesso!', false)
        ->assertDontSee('existem matrículas em andamento', false);

    expect($year->refresh()->andamento)->toBe(2);
});

// Defeito conhecido: ao encontrar matrículas em andamento a tela emite o aviso
// mas não interrompe a execução, e finaliza o ano letivo do mesmo jeito.
test('defeito: finaliza o ano mesmo avisando que há matrículas em andamento', function () {
    $year = ($this->year)(1);

    LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_escola' => $this->school,
        'ano' => 2030,
        'ativo' => 1,
        'ultima_matricula' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    ($this->act)('finalizar')
        ->assertOk()
        ->assertSee('Não foi possível finalizar o ano letivo existem matrículas em andamento!', false)
        ->assertSee('Ano letivo finalizado com sucesso!', false);

    expect($year->refresh()->andamento)->toBe(2);
});

test('volta para o detalhe da escola quando a ação não cabe no estado do ano', function (int $andamento, string $action) {
    $year = ($this->year)($andamento);

    ($this->act)($action)->assertRedirect("{$this->detail}#ano_letivo'");

    expect($year->refresh()->andamento)->toBe($andamento);
})->with([
    'iniciar um ano já em andamento' => [1, 'iniciar'],
    'finalizar um ano não iniciado' => [0, 'finalizar'],
    'ação desconhecida' => [1, 'outra'],
]);

// Defeito conhecido: um ano letivo inativo é achado pela primeira consulta e
// não pela segunda; a tela cai no ramo de erro, que nas duas ações diz
// "finalizar", inclusive quando a ação era iniciar.
test('defeito: avisa erro "ao finalizar" quando o ano letivo está inativo', function (int $andamento, string $action) {
    $year = ($this->year)($andamento, ['ativo' => 0]);

    ($this->act)($action)->assertOk()->assertSee('Erro ao finalizar o ano letivo!', false);

    expect($year->refresh()->andamento)->toBe($andamento);
})->with([
    'ao iniciar' => [0, 'iniciar'],
    'ao finalizar' => [1, 'finalizar'],
]);
