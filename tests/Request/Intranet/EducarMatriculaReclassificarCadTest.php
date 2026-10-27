<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

/**
 * Só as recusas e os desvios da reclassificação são testáveis: quando ela dá
 * certo a tela emite um alerta e chama `exit`, que encerraria a suíte. Por
 * isso todo envio daqui termina em uma recusa ou em um redirecionamento.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->user->institution;
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'data_matricula' => '2030-02-10']);
    $this->second = $this->scenario->grade('Segundo Ano');

    $this->url = "/intranet/educar_matricula_reclassificar_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->list = 'educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey();

    // Uma reclassificação com data anterior à da matrícula: passa pelo bloqueio
    // da LDB e é recusada logo depois, sem chegar a gravar.
    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ref_ref_cod_serie' => $this->second->getKey(),
        'data_cancel' => '05/02/2030',
        'descricao_reclassificacao' => 'Avaliação diagnóstica',
    ]);

    $this->dateRefusal = 'Data em que deixou de frequentar não pode ser inferior a data da matrícula.';
});

test('abre com o aluno, os cursos da escola no ano da matrícula e a data de hoje', function () {
    // Um curso que a escola só ofereceu em outro ano não entra na lista.
    LegacySchoolCourseFactory::new()->create([
        'ref_cod_escola' => $this->scenario->school,
        'ref_cod_curso' => LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'nm_curso' => 'Curso Antigo']),
        'anos_letivos' => '{2029}',
    ]);

    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['ref_cod_escola'])->toBe((string) $this->scenario->school->getKey())
        ->and($fields['serie_matricula'])->toBe((string) $this->scenario->grade->getKey())
        ->and($fields['data_cancel'])->toBe(now()->format('d/m/Y'))
        ->and(array_keys(LegacyForm::from($html)->options('ref_cod_curso')))->toBe([$this->scenario->course->getKey()]);
});

test('volta para as matrículas do aluno quando a matrícula não está em andamento ou não existe', function () {
    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    $this->get($this->url)->assertRedirect($this->list);

    $this->get("/intranet/educar_matricula_reclassificar_cad.php?ref_cod_matricula=999999999&ref_cod_aluno={$this->student->getKey()}")
        ->assertRedirect($this->list);
});

test('reclassificar para a mesma série só volta para o detalhe da matrícula', function () {
    $this->post($this->url, ($this->payload)([
        'ref_ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_ref_cod_serie_antiga' => $this->scenario->grade->getKey(),
    ]))->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});

test('recusa a data anterior à da matrícula', function (?string $registrationDate, string $created) {
    $this->registration->update(['data_matricula' => $registrationDate, 'data_cadastro' => $created]);

    $this->post($this->url, ($this->payload)())->assertOk()->assertSee($this->dateRefusal);

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
})->with([
    'pela data da matrícula' => ['2030-02-10', '2030-01-01 08:00:00'],
    // Sem data de matrícula, vale a data de cadastro.
    'pela data de cadastro' => [null, '2030-02-10 08:00:00'],
]);

test('quando a instituição proíbe, veda a reclassificação na educação infantil e no 1º ano do fundamental', function (int $stage, string $message) {
    $this->institution->update(['proibir_reclassificacao_educacao_infantil_primeiro_ano' => true]);
    $this->scenario->grade->update(['etapa_educacenso' => $stage]);

    $this->post($this->url, ($this->payload)())->assertOk()->assertSee($message)->assertDontSee($this->dateRefusal);

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
})->with([
    'creche' => [1, 'Fica vedada a reclassificação de alunos da educação infantil.'],
    'pré-escola' => [2, 'Fica vedada a reclassificação de alunos da educação infantil.'],
    'infantil unificada' => [3, 'Fica vedada a reclassificação de alunos da educação infantil.'],
    '1º ano do fundamental' => [14, 'é vedada a reclassificação no 1º ano do ensino fundamental (ciclo de alfabetização).'],
]);

test('a vedação da LDB não vale para as outras etapas nem com a proibição desligada', function (bool $forbid, int $stage) {
    $this->institution->update(['proibir_reclassificacao_educacao_infantil_primeiro_ano' => $forbid]);
    $this->scenario->grade->update(['etapa_educacenso' => $stage]);

    // Passa pela vedação e para na regra seguinte, a da data.
    $this->post($this->url, ($this->payload)())->assertOk()->assertSee($this->dateRefusal)->assertDontSee('LDB');
})->with([
    'proibição ligada, 2º ano do fundamental' => [true, 15],
    'proibição desligada, creche' => [false, 1],
]);

test('volta para as matrículas do aluno quando a matrícula deixa de estar em andamento antes do envio', function () {
    $payload = ($this->payload)(['data_cancel' => '15/05/2030']);

    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    $this->post($this->url, $payload)->assertRedirect($this->list);
});

// Defeito conhecido: a vedação da LDB exige um array, e a busca de uma
// matrícula inexistente devolve falso; a tela estoura antes de chegar ao
// redirecionamento que trata esse caso.
test('defeito: estoura quando a matrícula enviada não existe', function () {
    $this->post($this->url, ($this->payload)(['cod_matricula' => 999999999]))->assertServerError();
});

test('a ação de excluir não faz nada', function () {
    $this->post($this->url, ($this->payload)(['tipoacao' => 'Excluir']))->assertOk();

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});

test('volta para as matrículas do aluno quando o usuário só pode visualizar a reclassificação', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(1004, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect($this->list);
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect();
})->with([
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(1004)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
