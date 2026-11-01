<?php

use App\Events\NotificationEvent;
use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\Notification;
use App\Services\ComponentBatchManagerService;
use App\Services\iDiarioService;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyExemptionStageFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Database\Factories\LegacyStudentDescriptiveOpinionFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Event::fake([NotificationEvent::class]);

    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->school = $this->scenario->school;
    $this->grade = $this->scenario->grade;
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma A']);

    // Sem a configuração geral da instituição, o serviço não sabe dizer se o
    // iDiário está integrado; começa desligado.
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'url_novo_educacao' => null,
        'token_novo_educacao' => null,
    ]);

    // Matemática é oferecida só em 2030; História em 2029 e 2030, para que a
    // execução remova o ano de uma e apague a outra.
    $this->math = $this->scenario->discipline('Matemática');
    $this->history = $this->scenario->discipline('História');
    LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->update(['anos_letivos' => '{2029,2030}']);
    LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $this->history->getKey())->update(['anos_letivos' => '{2029,2030}']);

    $this->service = new ComponentBatchManagerService;

    $this->params = fn (array $overrides = []) => $overrides + [
        'year' => 2030,
        'institution_id' => $this->scenario->institution,
        'school_ids' => [$this->school->getKey()],
        'course_ids' => [$this->scenario->course->getKey()],
        'grade_ids' => [$this->grade->getKey()],
        'discipline_ids' => [$this->math->getKey(), $this->history->getKey()],
        'remove_records' => true,
        'unlink_class_components' => true,
        'unlink_teacher_disciplines' => true,
        'unlink_school_grade_disciplines' => true,
        'unlink_grade_components' => true,
        'remove_exemptions' => true,
        'user_id' => $this->user->getKey(),
    ];

    // Um aluno da turma com nota, média, falta, parecer e dispensa no
    // componente, que também está na turma e com a professora.
    $this->records = function (LegacyDiscipline $discipline, string $student = 'Bia Borges'): LegacyRegistration {
        $registration = $this->scenario->enrolled($student, $this->schoolClass)->registration;

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $this->grade,
            'escola_id' => $this->school,
            'turma_id' => $this->schoolClass,
        ]);

        EmployeeScenario::named("Professora de {$discipline->nome}", $this->scenario->institution)
            ->classLink($this->schoolClass, [], [$discipline]);

        $score = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);
        LegacyDisciplineScoreFactory::new()->create(['nota_aluno_id' => $score, 'componente_curricular_id' => $discipline, 'etapa' => 1]);
        LegacyDisciplineScoreAverageFactory::new()->create(['nota_aluno_id' => $score, 'componente_curricular_id' => $discipline]);
        LegacyDisciplineAbsenceFactory::new()->create([
            'falta_aluno_id' => LegacyStudentAbsenceFactory::new()->create(['matricula_id' => $registration]),
            'componente_curricular_id' => $discipline,
            'etapa' => 1,
        ]);
        LegacyDisciplineDescriptiveOpinionFactory::new()->create([
            'parecer_aluno_id' => LegacyStudentDescriptiveOpinionFactory::new()->create(['matricula_id' => $registration]),
            'componente_curricular_id' => $discipline,
            'etapa' => 1,
        ]);
        LegacyExemptionStageFactory::new()->create([
            'ref_cod_dispensa' => LegacyDisciplineExemptionFactory::new()->create([
                'ref_cod_matricula' => $registration,
                'ref_cod_disciplina' => $discipline,
                'ref_cod_escola' => $this->school,
                'ref_cod_serie' => $this->grade,
            ]),
            'etapa' => 1,
        ]);

        return $registration;
    };

    $this->operation = function (array $overrides = []): ComponentBatchOperation {
        $data = ($this->params)($overrides);

        return ComponentBatchOperation::query()->create([
            'user_id' => $this->user->getKey(),
            'data' => $data + ['preview_counts' => $this->service->calculatePreview($data)],
        ]);
    };

    $this->idiario = function () {
        config([
            'legacy.config.url_novo_educacao' => 'https://idiario.example',
            'legacy.config.token_novo_educacao' => 'token',
        ]);

        return $this->mock(iDiarioService::class);
    };

    $this->count = fn (string $table, array $where = []) => DB::table($table)->where($where)->count();
});

test('o preview conta cada tipo de registro dos filtros', function () {
    ($this->records)($this->math);

    expect($this->service->calculatePreview(($this->params)()))->toBe([
        'turma_count' => 1,
        'idiario' => null,
        'nota_media' => 1,
        'nota' => 1,
        'falta' => 1,
        'parecer' => 1,
        'componente_turma' => 1,
        'professor_disciplina' => 1,
        'professor_turma' => 1,
        'escola_serie_disciplina' => 2,
        'componente_ano_escolar' => 2,
        'dispensa' => 1,
    ]);
});

test('o preview só conta o que foi pedido', function () {
    ($this->records)($this->math);

    expect($this->service->calculatePreview(($this->params)([
        'remove_records' => false,
        'unlink_class_components' => false,
        'unlink_teacher_disciplines' => false,
        'unlink_school_grade_disciplines' => false,
        'unlink_grade_components' => false,
        'remove_exemptions' => false,
    ])))->toBe(['turma_count' => 1, 'idiario' => null]);
});

test('o preview consulta o iDiário quando ele está configurado', function () {
    ($this->idiario)()->shouldReceive('getDisciplineRecordsCount')->once()->andReturn(['notas' => ['count' => 3]]);

    expect($this->service->calculatePreview(($this->params)())['idiario'])->toBe(['notas' => ['count' => 3]]);
});

test('o preview guarda o erro do iDiário em vez de falhar', function () {
    ($this->idiario)()->shouldReceive('getDisciplineRecordsCount')->andThrow(new RuntimeException('fora do ar'));

    expect($this->service->calculatePreview(($this->params)())['idiario'])->toBe(['error' => 'fora do ar']);
});

test('o preview pula o iDiário quando pedido', function () {
    ($this->idiario)()->shouldNotReceive('getDisciplineRecordsCount');

    expect($this->service->calculatePreview(($this->params)(['skip_idiario' => true]))['idiario'])->toBeNull();
});

test('o preview separa os vínculos protegidos e detalha quem os bloqueia', function () {
    // Matemática na série da escola só em 2030, mas usada numa turma de 2029:
    // o vínculo escola/série fica protegido.
    $old = $this->scenario->schoolClass(['nm_turma' => 'Turma de 2029', 'ano' => 2029]);
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $this->math,
        'ano_escolar_id' => $this->grade,
        'escola_id' => $this->school,
        'turma_id' => $old,
    ]);

    // História oferecida na série por outra escola em 2030: o componente da
    // série fica protegido.
    $other = LegacySchoolFactory::new()->withName('Escola Beta')->create(['ref_cod_instituicao' => $this->scenario->institution]);
    LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $other, 'ref_cod_serie' => $this->grade, 'anos_letivos' => '{2030}']);
    LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $other,
        'ref_ref_cod_serie' => $this->grade,
        'ref_cod_disciplina' => $this->history,
        'anos_letivos' => '{2030}',
    ]);

    $params = ($this->params)();
    $preview = $this->service->calculatePreview($params);

    expect($preview)->toMatchArray([
        'escola_serie_disciplina' => 1,
        'escola_serie_disciplina_skipped' => 1,
        'componente_ano_escolar' => 1,
        'componente_ano_escolar_skipped' => 1,
    ]);

    expect($this->service->getProtectionDetails($params, $preview))->toBe([
        'componente_ano_escolar' => [
            ['componente' => 'História', 'serie' => 'Primeiro Ano', 'escolas' => ['Escola Beta']],
        ],
        'escola_serie_disciplina' => [
            ['componente' => 'Matemática', 'escola' => 'Escola Aurora', 'serie' => 'Primeiro Ano', 'anos_bloqueando' => [2029]],
        ],
    ]);
});

test('sem vínculos protegidos não há detalhes de proteção', function () {
    $params = ($this->params)();

    expect($this->service->getProtectionDetails($params, ['escola_serie_disciplina_skipped' => 0]))->toBe([]);
});

test('soma os totais do i-Educar e do iDiário ignorando turmas, vínculos e protegidos', function () {
    expect(ComponentBatchManagerService::sumPreviewCounts([
        'turma_count' => 5,
        'professor_turma' => 4,
        'escola_serie_disciplina_skipped' => 3,
        'nota' => 2,
        'dispensa' => 1,
        'idiario' => ['notas' => ['count' => '7'], 'erro' => 'texto', 'faltas' => ['count' => 1]],
    ]))->toBe(['totalIeducar' => 3, 'totalIdiario' => 8]);

    expect(ComponentBatchManagerService::sumIdiarioCounts(['error' => 'fora do ar']))->toBe(0);
    expect(ComponentBatchManagerService::sumIdiarioCounts(null))->toBe(0);
});

test('avisa quando se desvincula sem remover lançamentos, usando os lançamentos já contados', function () {
    $params = ($this->params)(['remove_records' => false]);

    expect($this->service->buildWarnings($params, ['turma_count' => 1, 'nota' => 2]))->toBe([
        'Existem 2 lançamentos. Desvincular sem remover pode gerar inconsistência.',
    ]);
});

test('avisa quando se desvincula sem remover lançamentos, recontando quando o preview não os trouxe', function () {
    ($this->records)($this->math);

    $params = ($this->params)(['remove_records' => false]);

    expect($this->service->buildWarnings($params, ['turma_count' => 1]))->toBe([
        'Existem 4 lançamentos. Desvincular sem remover pode gerar inconsistência.',
    ]);
});

test('sem lançamentos não avisa sobre desvincular sem remover', function () {
    $params = ($this->params)(['remove_records' => false]);

    expect($this->service->buildWarnings($params, ['turma_count' => 1]))->toBe([]);
});

test('avisa sobre vínculos de professor e de turma que ficarão para trás', function () {
    $params = ($this->params)([
        'unlink_teacher_disciplines' => false,
    ]);

    expect($this->service->buildWarnings($params, ['nota' => 1]))->toBe([
        'Vínculos de professor/disciplina continuarão apontando para componentes removidos da turma.',
    ]);

    $params = ($this->params)([
        'unlink_class_components' => false,
        'unlink_teacher_disciplines' => false,
    ]);

    expect($this->service->buildWarnings($params, ['nota' => 1]))->toBe([
        'Componentes continuarão vinculados nas turmas.',
    ]);
});

test('executa a exclusão no i-Educar, guarda o backup e confere o que sobrou', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)();

    $counts = $this->service->execute($operation);

    expect($counts)->toEqual([
        'nota_media' => 1,
        'nota' => 1,
        'falta' => 1,
        'parecer' => 1,
        'componente_turma' => 1,
        'professor_disciplina' => 1,
        'professor_turma' => 1,
        'escola_serie_disciplina' => 2,
        'componente_ano_escolar' => 2,
        'dispensa' => 1,
    ]);

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::COMPLETED)
        ->and($operation->data['result_counts'])->toEqual($counts)
        ->and($operation->data['verification_warnings'])->toBe([])
        ->and(ComponentBatchManagerService::sumPreviewCounts($operation->data['post_counts'])['totalIeducar'])->toBe(0);

    // Matemática some; História perde só o ano de 2030.
    expect(($this->count)('pmieducar.escola_serie_disciplina', ['ref_cod_disciplina' => $this->math->getKey()]))->toBe(0)
        ->and(($this->count)('modules.componente_curricular_ano_escolar', ['componente_curricular_id' => $this->math->getKey()]))->toBe(0)
        ->and(LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->value('anos_letivos'))->toBe('{2029}')
        ->and(LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $this->history->getKey())->value('anos_letivos'))->toBe('{2029}')
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(0)
        ->and(($this->count)('pmieducar.dispensa_disciplina', ['ref_cod_disciplina' => $this->math->getKey()]))->toBe(0);

    $backup = $operation->backup;
    $exemption = $backup['deleted']['pmieducar.dispensa_disciplina']['rows'][0]['cod_dispensa'];
    expect(($this->count)('pmieducar.dispensa_etapa', ['ref_cod_dispensa' => $exemption]))->toBe(0);
    // O jsonb não guarda a ordem das chaves.
    expect(array_keys($backup['deleted']))->toEqualCanonicalizing([
        'modules.nota_componente_curricular_media',
        'modules.nota_componente_curricular',
        'modules.falta_componente_curricular',
        'modules.parecer_componente_curricular',
        'modules.componente_curricular_turma',
        'modules.professor_turma_disciplina',
        'pmieducar.escola_serie_disciplina',
        'modules.componente_curricular_ano_escolar',
        'pmieducar.dispensa_etapa',
        'pmieducar.dispensa_disciplina',
    ])
        ->and(array_keys($backup['updated']))->toEqualCanonicalizing(['pmieducar.escola_serie_disciplina', 'modules.componente_curricular_ano_escolar'])
        ->and($backup['updated']['pmieducar.escola_serie_disciplina']['rows'][0]['anos_letivos'])->toBe([2029, 2030])
        ->and($backup['deleted']['pmieducar.escola_serie_disciplina']['unique'])->toBe(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
        ->and($backup['touched']['modules.professor_turma']['turma_ids'])->toBe([$this->schoolClass->getKey()]);
});

test('só os lançamentos: não procura turmas nem mexe nos vínculos', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)([
        'unlink_class_components' => false,
        'unlink_teacher_disciplines' => false,
        'unlink_school_grade_disciplines' => false,
        'unlink_grade_components' => false,
        'remove_exemptions' => false,
    ]);

    expect($this->service->execute($operation))->toEqual(['nota_media' => 1, 'nota' => 1, 'falta' => 1, 'parecer' => 1])
        ->and(($this->count)('modules.componente_curricular_turma', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1)
        ->and($operation->refresh()->backup)->not->toHaveKey('touched');
});

test('sem registros a executar só conclui a operação', function () {
    $operation = ($this->operation)(['discipline_ids' => []]);

    expect($this->service->execute($operation))->toBe([]);

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::COMPLETED)
        ->and($operation->backup)->toBeNull()
        ->and($operation->data['result_counts'])->toBe([]);
});

test('a execução ignora os vínculos de série que não têm o ano', function () {
    LegacyDisciplineAcademicYear::query()->whereIn('componente_curricular_id', [$this->math->getKey(), $this->history->getKey()])->update(['anos_letivos' => '{2029}']);
    LegacySchoolGradeDiscipline::query()->whereIn('ref_cod_disciplina', [$this->math->getKey(), $this->history->getKey()])->update(['anos_letivos' => '{2029}']);
    ($this->records)($this->math);

    $operation = ($this->operation)();
    $counts = $this->service->execute($operation);

    expect($counts['escola_serie_disciplina'])->toBe(0)
        ->and($counts['componente_ano_escolar'])->toBe(0)
        ->and($operation->refresh()->backup)->not->toHaveKey('updated');
});

test('avisa quando sobram registros depois da exclusão', function () {
    $service = $this->partialMock(ComponentBatchManagerService::class);
    $service->shouldReceive('calculatePreview')->andReturn(['turma_count' => 1, 'nota' => 2, 'professor_turma' => 1, 'desconhecido' => 1]);

    $operation = ComponentBatchOperation::query()->create([
        'user_id' => $this->user->getKey(),
        'data' => ($this->params)() + ['preview_counts' => ['nota' => 2]],
    ]);

    $service->execute($operation);

    expect($operation->refresh()->data['verification_warnings'])->toBe([
        'Notas: 2 registros remanescentes após exclusão.',
        'desconhecido: 1 registros remanescentes após exclusão.',
    ]);
});

test('com o iDiário na fila, a operação fica aguardando o callback', function () {
    $idiario = ($this->idiario)();
    $idiario->shouldReceive('getDisciplineRecordsCount')->andReturn(['notas' => ['count' => 2]]);
    $idiario->shouldReceive('deleteDisciplineRecords')->once()->andReturn(['queued' => true]);
    ($this->records)($this->math);

    $operation = ($this->operation)();

    expect($this->service->execute($operation))->toBe([]);

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::RUNNING)
        ->and($operation->data['awaiting_callback'])->toBeTrue()
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1);
});

test('com o iDiário síncrono, exclui lá e aqui e avisa se as contagens divergem', function () {
    $idiario = ($this->idiario)();
    $idiario->shouldReceive('getDisciplineRecordsCount')->once()->andReturn(['notas' => ['count' => 2]]);
    ($this->records)($this->math);
    $operation = ($this->operation)();

    $idiario->shouldReceive('deleteDisciplineRecords')->once()->andReturn(['success' => true, 'deleted' => 5]);
    $idiario->shouldReceive('getDisciplineRecordsCount')->once()->andReturn([]);

    $this->service->execute($operation);

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::COMPLETED)
        ->and($operation->data['idiario_deleted'])->toBe(5)
        ->and($operation->data['post_idiario'])->toBe([])
        ->and($operation->data['verification_warnings'])->toBe([
            'i-Diário: destroy_batch retornou 5 exclusões, mas o preview contava 2. Recontagem confirmou 0 remanescentes.',
        ])
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(0);
});

test('falha quando o iDiário não confirma a exclusão', function () {
    $idiario = ($this->idiario)();
    $idiario->shouldReceive('getDisciplineRecordsCount')->andReturn(['notas' => ['count' => 2]]);
    $idiario->shouldReceive('deleteDisciplineRecords')->andReturn(['success' => false, '_status_code' => 500]);
    ($this->records)($this->math);
    $operation = ($this->operation)();

    expect(fn () => $this->service->execute($operation))
        ->toThrow(RuntimeException::class, 'Não foi possível excluir os registros do i-Diário. Verifique se o serviço está disponível e tente novamente.');

    expect($operation->refresh()->data['idiario_error_detail'])->toBe(['success' => false, '_status_code' => 500]);
});

test('falha quando ainda sobram registros no iDiário', function () {
    $idiario = ($this->idiario)();
    $idiario->shouldReceive('getDisciplineRecordsCount')->once()->andReturn(['notas' => ['count' => 2]]);
    ($this->records)($this->math);
    $operation = ($this->operation)();

    $idiario->shouldReceive('deleteDisciplineRecords')->andReturn(['success' => true]);
    $idiario->shouldReceive('getDisciplineRecordsCount')->once()->andReturn(['notas' => ['count' => 1]]);

    expect(fn () => $this->service->execute($operation))
        ->toThrow(RuntimeException::class, 'Exclusão do i-Diário incompleta: 1 registros remanescentes. Operação no i-Educar cancelada.');

    expect(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1);
});

test('o callback do iDiário conclui a operação e avisa o usuário', function () {
    ($this->idiario)()->shouldReceive('getDisciplineRecordsCount')->andReturn([]);
    ($this->records)($this->math);
    $operation = ($this->operation)();
    $operation->update(['status_id' => ComponentBatchStatus::RUNNING->value]);

    $this->service->processCallback($operation, ['success' => true, 'deleted' => 2]);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::COMPLETED)
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(0);

    $notification = Notification::query()->where('user_id', $this->user->getKey())->latest('id')->first();
    expect($notification->text)->toBe('Remoção de componentes concluída com sucesso.')
        ->and($notification->link)->toBe(route('component-batch-manager.show', $operation));
});

test('o callback com erro do iDiário marca a falha e avisa o usuário', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)();
    $operation->update(['status_id' => ComponentBatchStatus::RUNNING->value]);

    $this->service->processCallback($operation, ['success' => false]);

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::FAILED)
        ->and($operation->error_message)->toBe('Não foi possível excluir os registros do i-Diário. Verifique se o serviço está disponível e tente novamente.')
        ->and($operation->data['error_detail'])->toBe($operation->error_message)
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1);

    expect(Notification::query()->where('user_id', $this->user->getKey())->latest('id')->value('text'))
        ->toBe('Erro na remoção de componentes. Clique para ver detalhes.');
});

test('erros inesperados viram uma mensagem genérica, guardando o detalhe', function () {
    $operation = ($this->operation)();

    $this->service->failed($operation, 'SQLSTATE[23503] violação de chave');

    $operation->refresh();
    expect($operation->status())->toBe(ComponentBatchStatus::FAILED)
        ->and($operation->error_message)->toBe('Ocorreu um erro inesperado durante a execução. Contate o administrador.')
        ->and($operation->data['error_detail'])->toBe('SQLSTATE[23503] violação de chave');
});

// As dispensas ficam de fora da restauração: a tabela de etapas da dispensa
// não tem a chave única que a reinserção usa no ON CONFLICT.
test('restaura o backup reinserindo o que foi apagado e devolvendo o ano ao que foi alterado', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)(['remove_exemptions' => false]);
    $this->service->execute($operation);
    $operation->refresh();

    expect(($this->count)('pmieducar.escola_serie_disciplina_excluidos', ['ref_cod_disciplina' => $this->math->getKey()]))->toBe(1)
        ->and(($this->count)('modules.componente_curricular_turma_excluidos', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1);

    $summary = $this->service->restore($operation);

    // As tabelas com linhas alteradas e apagadas ficam com a última ação.
    expect($summary)->toBe([
        'pmieducar.escola_serie_disciplina' => ['action' => 'reinseridos', 'count' => 1],
        'modules.componente_curricular_ano_escolar' => ['action' => 'reinseridos', 'count' => 1],
        'modules.componente_curricular_turma' => ['action' => 'reinseridos', 'count' => 1],
        'modules.professor_turma_disciplina' => ['action' => 'reinseridos', 'count' => 1],
        'modules.nota_componente_curricular' => ['action' => 'reinseridos', 'count' => 1],
        'modules.falta_componente_curricular' => ['action' => 'reinseridos', 'count' => 1],
        'modules.parecer_componente_curricular' => ['action' => 'reinseridos', 'count' => 1],
        'modules.nota_componente_curricular_media' => ['action' => 'reinseridos', 'count' => 1],
    ]);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::RESTORED)
        ->and(($this->count)('pmieducar.escola_serie_disciplina', ['ref_cod_disciplina' => $this->math->getKey()]))->toBe(1)
        ->and(($this->count)('pmieducar.escola_serie_disciplina_excluidos', ['ref_cod_disciplina' => $this->math->getKey()]))->toBe(0)
        ->and(($this->count)('modules.componente_curricular_turma_excluidos', ['componente_curricular_id' => $this->math->getKey()]))->toBe(0)
        ->and(LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->value('anos_letivos'))->toBe('{2029,2030}')
        ->and(LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $this->history->getKey())->value('anos_letivos'))->toBe('{2029,2030}')
        ->and(($this->count)('modules.nota_componente_curricular', ['componente_curricular_id' => $this->math->getKey()]))->toBe(1);

    expect($this->service->calculatePreview(($this->params)()))->toMatchArray([
        'nota' => 1,
        'componente_turma' => 1,
        'professor_disciplina' => 1,
        'escola_serie_disciplina' => 2,
        'componente_ano_escolar' => 2,
    ]);
});

test('a restauração padrão mescla os anos e mantém o que já existe; a forçada sobrescreve com o backup', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)(['remove_exemptions' => false]);
    $this->service->execute($operation);
    $operation->refresh();

    // Entre a exclusão e a restauração, alguém mexeu nos anos de História e
    // recriou o vínculo de Matemática com a série.
    LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->update(['anos_letivos' => '{2028}']);
    LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $this->math->getKey())->update(['anos_letivos' => '{2031}']);

    $this->service->restore($operation);

    expect(LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->value('anos_letivos'))->toBe('{2028,2029,2030}');

    $this->service->restore($operation, forceBackup: true);

    expect(LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $this->history->getKey())->value('anos_letivos'))->toBe('{2029,2030}');
});

test('a restauração forçada sobrescreve os registros reinseridos que já existiam', function () {
    ($this->records)($this->math);
    $operation = ($this->operation)(['remove_exemptions' => false]);
    $this->service->execute($operation);
    $operation->refresh();

    $row = $operation->backup['deleted']['modules.componente_curricular_ano_escolar']['rows'][0];
    DB::table('modules.componente_curricular_ano_escolar')->insert(array_merge($row, ['anos_letivos' => '{2031}', 'carga_horaria' => 1]));

    $this->service->restore($operation, forceBackup: true);

    expect(LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $this->math->getKey())->first())
        ->anos_letivos->toBe('{2030}')
        ->carga_horaria->toEqual(80);
});

test('não restaura uma operação sem backup', function () {
    $operation = ($this->operation)();

    expect(fn () => $this->service->restore($operation))
        ->toThrow(RuntimeException::class, 'Operação não possui backup para restaurar.');
});

test('recusa um backup sem a chave primária das linhas alteradas', function () {
    $operation = ($this->operation)();
    $operation->update(['backup' => [
        'updated' => ['pmieducar.escola_serie_disciplina' => ['pk' => ['id'], 'rows' => [
            ['anos_letivos' => 'não é lista'],
            ['anos_letivos' => [2030]],
        ]]],
    ]]);

    expect(fn () => $this->service->restore($operation))
        ->toThrow(RuntimeException::class, "Backup corrompido: coluna PK 'id' ausente na tabela pmieducar.escola_serie_disciplina.");
});

test('tabelas sem linhas no backup contam zero na restauração', function () {
    $operation = ($this->operation)();
    $operation->update(['backup' => [
        'deleted' => ['pmieducar.dispensa_etapa' => ['pk' => ['ref_cod_dispensa', 'etapa'], 'rows' => []]],
    ]]);

    expect($this->service->restore($operation))->toBe([
        'pmieducar.dispensa_etapa' => ['action' => 'reinseridos', 'count' => 0],
    ]);
});

test('sem colunas a atualizar o conflito não faz nada e sem linhas não apaga nada', function () {
    $call = fn (string $method, ...$arguments) => (fn () => $this->{$method}(...$arguments))->call($this->service);

    expect($call('buildOnConflictClause', ['id', 'nome'], ['id', 'nome'], true))->toBe('DO NOTHING');

    DB::enableQueryLog();
    $call('deleteByColumns', 'pmieducar.serie', ['cod_serie'], []);

    expect(DB::getQueryLog())->toBe([]);
});
