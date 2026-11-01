<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    Portabilis_Utils_User::$_currentUserId = null;
    Portabilis_Utils_User::$_nivelAcesso = null;
    Portabilis_Utils_User::$_permissoes = null;

    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->registration = fn (array $attributes = []) => $this->scenario->registration($attributes + ['ref_cod_aluno' => $this->student]);
    $this->detail = fn ($registration) => $this->get('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey())->assertOk();

    // Uma matrícula já enturmada numa turma com as características pedidas.
    $this->enrolled = function (array $schoolClass = [], array $enrollment = [], array $registration = []) {
        $registration = ($this->registration)($registration);

        $this->scenario->enroll($registration, $this->scenario->schoolClass($schoolClass + ['nm_turma' => 'Turma Alfa']), $enrollment + [
            'turno_id' => null,
            'data_enturmacao' => '2030-02-10',
        ]);

        return $registration;
    };

    $this->transfer = fn ($registration, array $attributes = []) => LegacyTransferRequestFactory::new()->create($attributes + [
        'ref_cod_matricula_saida' => $registration,
        'ref_cod_matricula_entrada' => null,
        'ref_cod_escola_destino' => null,
        'observacao' => 'Mudou de cidade',
    ]);
});

test('mostra os dados da matrícula e da enturmação', function () {
    $registration = ($this->enrolled)(
        ['turma_turno_id' => $this->scenario->period(2)],
        [],
        ['observacoes' => 'Entrou por ordem judicial']
    );

    ($this->detail)($registration)->assertSeeInOrder([
        'Número Matrícula', (string) $registration->getKey(),
        'Aluno', 'Ana Alves',
        'Instituição', $this->user->institution->nm_instituicao,
        'Escola', 'Escola Aurora',
        'Curso', 'Ensino Fundamental',
        'Série', 'Primeiro Ano',
        'Observações', 'Entrou por ordem judicial',
        'Turma', 'Turma Alfa',
        'Turno', 'Vespertino',
        'Data Enturmação', '10/02/2030',
        'Situação', 'Cursando',
        'Formando', 'Não',
    ], false);
});

test('mostra o turno da enturmação quando ela tem turno próprio', function () {
    $registration = ($this->registration)();

    foreach (['Turma Manhã' => 1, 'Turma Tarde' => 2, 'Turma Noite' => 3] as $name => $shift) {
        $this->scenario->enroll($registration, $this->scenario->schoolClass(['nm_turma' => $name]), ['turno_id' => $shift, 'sequencial' => $shift]);
    }

    ($this->detail)($registration)
        ->assertSee('Turma Manhã<br />Turma Tarde<br />Turma Noite', false)
        ->assertSee('Matutino<br />Vespertino<br />Noturno', false);
});

test('a enturmação com turno integral não tem nome de turno próprio', function () {
    $registration = ($this->enrolled)(['turma_turno_id' => $this->scenario->period(4)], ['turno_id' => 4]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Turma', 'Turma Alfa', 'Turno', 'Data Enturmação'])
        ->assertDontSee('Integral');
});

test('sem turno próprio na enturmação mostra o turno da turma', function (int $shift, string $name) {
    $registration = ($this->enrolled)(['turma_turno_id' => $this->scenario->period($shift)]);

    ($this->detail)($registration)->assertSeeInOrder(['Turno', $name, 'Data Enturmação']);
})->with([
    [1, 'Matutino'],
    [3, 'Noturno'],
]);

test('mostra o turno integral da turma e oferece a troca de turno', function () {
    $registration = ($this->enrolled)(['turma_turno_id' => $this->scenario->period(4)]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Turno', 'Integral'])
        ->assertSee('educar_matricula_turma_turno_cad.php?ref_cod_matricula=' . $registration->getKey(), false);
});

test('ignora a enturmação encerrada por transferência', function () {
    $registration = ($this->enrolled)();

    $this->scenario->enroll($registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Antiga']), [
        'ativo' => 0,
        'transferido' => true,
        'sequencial' => 2,
    ]);

    ($this->detail)($registration)->assertSee('Turma Alfa')->assertDontSee('Turma Antiga');
});

test('sem enturmação deixa turma, turno e data em branco e não oferece dispensa nem dependência', function () {
    $registration = ($this->registration)(['dependencia' => true]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Turma', 'Turno', 'Data Enturmação', 'Situação'])
        ->assertDontSee('educar_dispensa_disciplina_lst.php', false)
        ->assertDontSee('educar_disciplina_dependencia_lst.php', false);
});

test('oferece as ações da matrícula em andamento', function () {
    $registration = ($this->enrolled)();
    $key = $registration->getKey();

    ($this->detail)($registration)
        ->assertSee('Cancelar matrícula')
        ->assertSee("educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_dispensa_disciplina_lst.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_busca_ativa_lst.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_matricula_turma_lst.php?ref_cod_matricula={$key}&ano_letivo=2030", false)
        ->assertSee("educar_matricula_modalidade_ensino.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_abandono_cad.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_falecido_cad.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_matricula_reclassificar_cad.php?ref_cod_matricula={$key}", false)
        ->assertSee("educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$key}", false)
        ->assertSee('Deseja marcar a matrícula como formando?', false)
        ->assertSee('Histórico de enturmações')
        // Só oferece dependência para matrícula de dependência.
        ->assertDontSee('educar_disciplina_dependencia_lst.php', false)
        ->assertDontSee('educar_matricula_etapa_turma_cad.php', false)
        ->assertDontSee('educar_matricula_turma_tipo_aee_cad.php', false)
        ->assertDontSee('educar_matricula_turma_turno_cad.php', false);
});

test('oferece as disciplinas de dependência na matrícula de dependência enturmada', function () {
    $registration = ($this->enrolled)([], [], ['dependencia' => true]);

    ($this->detail)($registration)
        ->assertSee('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $registration->getKey(), false);
});

test('oferece a etapa do aluno em turma multisseriada e o tipo de AEE em turma de atendimento especializado', function () {
    $multi = ($this->enrolled)(['etapa_educacenso' => 3]);
    $aee = ($this->enrolled)(['tipo_atendimento' => '{5}']);

    ($this->detail)($multi)
        ->assertSee('educar_matricula_etapa_turma_cad.php?ref_cod_matricula=' . $multi->getKey(), false)
        ->assertDontSee('educar_matricula_turma_tipo_aee_cad.php', false);

    ($this->detail)($aee)
        ->assertSee('educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula=' . $aee->getKey(), false)
        ->assertDontSee('educar_matricula_etapa_turma_cad.php', false);
});

test('na matrícula formanda oferece desmarcar', function () {
    $registration = ($this->enrolled)([], [], ['formando' => 1]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Formando', 'Sim'])
        ->assertSee('Deseja desmarcar a matrícula como formando?', false);
});

test('com transferência solicitada só oferece cancelar a solicitação', function () {
    $registration = ($this->enrolled)();

    ($this->transfer)($registration, ['data_transferencia' => '2030-05-10']);

    ($this->detail)($registration)
        ->assertSee('Cancelar solicitação transferência')
        ->assertDontSee('Cancelar matrícula')
        ->assertDontSee('Deseja marcar a matrícula como formando?', false);
});

test('na matrícula transferida mostra a escola de destino interna e oferece cancelar a transferência', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);
    $destination = EnrollmentScenario::for($this->user, 2030)->school;

    ($this->transfer)($registration, ['ref_cod_escola_destino' => $destination]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Situação', 'Transferido', 'Escola destino', $destination->organization->fantasia, 'Observação', 'Mudou de cidade'], false)
        ->assertDontSee('Estado escola destino')
        ->assertSee('Cancelar transferência')
        ->assertDontSee('Cancelar matrícula');
});

test('na matrícula transferida para fora da rede mostra escola, estado e município de destino', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    ($this->transfer)($registration, [
        'escola_destino_externa' => 'Colégio Horizonte',
        'estado_escola_destino_externa' => 'Paraná',
        'municipio_escola_destino_externa' => 'Curitiba',
    ]);

    ($this->detail)($registration)
        ->assertSeeInOrder([
            'Escola destino', 'Colégio Horizonte',
            'Estado escola destino', 'Paraná',
            'Município escola destino', 'Curitiba',
            'Observação', 'Mudou de cidade',
        ]);
});

test('na matrícula transferida sem solicitação ativa oferece cancelar quando ela é a última do aluno', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    ($this->detail)($registration)->assertSee('Cancelar transferência');

    // Com uma matrícula mais nova em andamento, a transferência não pode mais ser cancelada.
    ($this->registration)();

    ($this->detail)($registration)->assertDontSee('Cancelar transferência');
});

test('na matrícula de aluno falecido mostra a observação', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::FALECIDO, 'observacao' => 'Óbito informado pela família']);

    ($this->detail)($registration)->assertSeeInOrder(['Situação', 'Falecido', 'Observação', 'Óbito informado pela família']);
});

test('na matrícula com abandono mostra o motivo e oferece desfazer', function () {
    $registration = ($this->enrolled)([], [], [
        'aprovado' => App_Model_MatriculaSituacao::ABANDONO,
        'ref_cod_abandono_tipo' => LegacyAbandonmentTypeFactory::new()->create(['nome' => 'Mudança de cidade']),
        'observacao' => 'Não voltou das férias',
    ]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Motivo - Deixou de Frequentar', 'Mudança de cidade', 'Observação', 'Não voltou das férias'])
        ->assertSee('showAlertTurnoDeixouFrequentar()', false)
        ->assertSee("deleteAbandono({$registration->getKey()})", false);
});

test('na matrícula com abandono sem motivo cadastrado deixa o motivo em branco', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::ABANDONO]);

    ($this->detail)($registration)->assertSee('Motivo - Deixou de Frequentar');
});

test('na matrícula reclassificada mostra a descrição, resumida quando longa, e oferece desfazer', function () {
    $long = str_repeat('Reclassificado por desempenho acima do esperado. ', 6);

    $short = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::RECLASSIFICADO, 'descricao_reclassificacao' => 'Avaliação diagnóstica']);
    $verbose = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::RECLASSIFICADO, 'descricao_reclassificacao' => $long]);
    $empty = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::RECLASSIFICADO, 'descricao_reclassificacao' => null]);

    ($this->detail)($short)
        ->assertSee("<span class='desc-red'>Avaliação diagnóstica</span>", false)
        ->assertSee("deleteReclassificacao({$short->getKey()})", false);

    ($this->detail)($verbose)->assertSee('...</span>', false)->assertSee(trim($long));

    ($this->detail)($empty)->assertDontSee("class='desc-red'", false);
});

test('na matrícula concluída do último ano oferece a saída da escola', function (int $status) {
    $registration = ($this->enrolled)([], [], ['aprovado' => $status]);

    ($this->detail)($registration)
        ->assertSee('educar_saida_escola_cad.php?ref_cod_matricula=' . $registration->getKey(), false)
        ->assertDontSee('Cancelar saída da escola');
})->with([
    'aprovado' => App_Model_MatriculaSituacao::APROVADO,
    'reprovado' => App_Model_MatriculaSituacao::REPROVADO,
    'aprovado com dependência' => App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA,
    'aprovado pelo conselho' => App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO,
    'reprovado por faltas' => App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS,
]);

test('não oferece a saída da escola quando o aluno tem matrícula em ano posterior', function () {
    $registration = ($this->enrolled)([], [], ['aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    ($this->registration)(['ano' => 2031]);

    ($this->detail)($registration)->assertDontSee('educar_saida_escola_cad.php', false);
});

test('com saída da escola registrada mostra a data e oferece cancelar', function () {
    $registration = ($this->enrolled)([], [], [
        'aprovado' => App_Model_MatriculaSituacao::APROVADO,
        'saida_escola' => true,
        'data_saida_escola' => '2030-12-20',
        'observacao' => 'Concluiu o curso',
    ]);

    ($this->detail)($registration)
        ->assertSeeInOrder(['Saída da escola', 'Sim', 'Data de saída da escola', '20/12/2030', 'Observação', 'Concluiu o curso'])
        ->assertSee("desfazerSaidaEscola({$registration->getKey()})", false)
        ->assertDontSee('educar_saida_escola_cad.php', false);
});

test('a quem só pode visualizar mostra os dados sem as ações', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $registration = ($this->enrolled)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->detail)($registration)
        ->assertSee('Ana Alves')
        ->assertDontSee('Cancelar matrícula')
        ->assertDontSee('educar_matricula_turma_lst.php', false)
        ->assertDontSee('Histórico de enturmações');
});

test('ao usuário de biblioteca não oferece ação nenhuma, mesmo com permissão de cadastro', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $registration = ($this->enrolled)();

    $this->actingAs(
        LegacyUserFactory::new()->withAccess(578)->withSchool($this->scenario->school)->create([
            'ref_cod_instituicao' => $this->scenario->institution,
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
        ])
    );

    ($this->detail)($registration)
        ->assertSee('Ana Alves')
        ->assertDontSee('Cancelar matrícula')
        ->assertDontSee('educar_matricula_turma_lst.php', false);
});

test('volta para a listagem de alunos avisando quando a matrícula não existe', function () {
    $this->get('/intranet/educar_matricula_det.php?cod_matricula=0')
        ->assertRedirect('educar_aluno_lst.php')
        ->assertSessionHas('error', 'Matrícula não encontrada. O registro não existe ou foi excluído. Acesse a matrícula pela ficha do aluno.');
});
