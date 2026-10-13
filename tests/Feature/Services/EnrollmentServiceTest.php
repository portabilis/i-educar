<?php

use App\Exceptions\Enrollment\CancellationDateAfterAcademicYearException;
use App\Exceptions\Enrollment\CancellationDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateAfterAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentSameTimeException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Exceptions\Enrollment\PreviousEnrollRegistrationDateException;
use App\Models\LegacyEnrollment;
use App\Services\EnrollmentService;
use App\User;
use Carbon\Carbon;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Support\Exceptions\Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $user = LegacyUserFactory::new()->unique()->make();

    $user = User::find($user->id);

    $schoolClass = LegacySchoolClassFactory::new()->create();

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    $this->schoolClass = $schoolClass;
    $this->service = new EnrollmentService($user);
});

/**
 * Cancelamento de enturmação com sucesso.
 *
 * @return void
 *
 * @throws Throwable
 */
test('cancel enrollment', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $result = $this->service->cancelEnrollment($enrollment, now());

    $this->assertTrue($result);
    $this->assertDatabaseHas($enrollment->getTable(), [
        'ref_cod_matricula' => $enrollment->ref_cod_matricula,
        'ref_cod_turma' => $enrollment->ref_cod_turma,
        'ativo' => 0,
    ]);
});

test('cancelamento que falha ao gravar desfaz e devolve falso', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);
    // O serviço só captura as exceções do i-Educar.
    LegacyEnrollment::saving(fn () => throw new Exception('banco indisponível'));

    $this->assertFalse($this->service->cancelEnrollment($enrollment, now()));
    $this->assertSame(1, $enrollment->refresh()->ativo);
});

/**
 * Erro ao cancelar uma enturmação devido a data de saída ser anterior ao
 * início do ano letivo.
 *
 * @return void
 *
 * @throws Throwable
 */
test('cancellation date before academic year exception', function () {
    $this->expectException(CancellationDateBeforeAcademicYearException::class);

    $stage = $this->schoolClass->stages()->first();

    $stage->data_inicio = now()->addDay();
    $stage->save();

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $enrollment->schoolClass->school->institution->permitir_matricula_fora_periodo_letivo = false;
    $enrollment->schoolClass->school->institution->save();

    $this->service->cancelEnrollment($enrollment, now());
});

/**
 * Erro ao cancelar uma enturmação devido a data de saída ser posterior ao
 * fim do ano letivo.
 *
 * @return void
 *
 * @throws Throwable
 */
test('cancellation date after academic year exception', function () {
    $this->expectException(CancellationDateAfterAcademicYearException::class);

    $stage = $this->schoolClass->stages()->first();

    $stage->data_fim = now()->subDay();
    $stage->save();

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $this->service->cancelEnrollment($enrollment, now());
});

/**
 * Erro ao cancelar uma enturmação devido a data de saída ser anterior que
 * a data de enturmação.
 *
 * @return void
 *
 * @throws Throwable
 */
test('previous cancellation date exception', function () {
    $this->expectException(PreviousCancellationDateException::class);

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $this->service->cancelEnrollment($enrollment, now()->subDay(1));
});

/**
 * Enturmação feita com sucesso.
 *
 * @return void
 */
test('enroll', function () {
    $enrollment = LegacyEnrollmentFactory::new()->make([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $result = $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );

    $this->assertInstanceOf(LegacyEnrollment::class, $result);
    $this->assertDatabaseHas($enrollment->getTable(), [
        'ref_cod_matricula' => $enrollment->registration_id,
        'ref_cod_turma' => $enrollment->school_class_id,
        'ativo' => 1,
    ]);
});

/**
 * Sem vagas na turma.
 *
 * @return void
 */
test('no vacancy exception', function () {
    $this->expectException(NoVacancyException::class);

    $enrollment = LegacyEnrollmentFactory::new()->make([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $enrollment->schoolClass->max_aluno = 0;

    $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );
});

/**
 * Existe uma outra enturmação ativa para a matrícula na turma.
 *
 * @return void
 */
test('exists active enrollment exception', function () {
    $this->expectException(ExistsActiveEnrollmentException::class);

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );
});

/**
 * Erro ao enturmar uma matrícula devido a data de entrada ser anterior ao
 * início do ano letivo.
 *
 * @return void
 *
 * @throws Throwable
 */
test('enroll date before academic year exception', function () {
    $this->expectException(EnrollDateBeforeAcademicYearException::class);

    $enrollment = LegacyEnrollmentFactory::new()->make([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $enrollment->schoolClass->school->institution->permitir_matricula_fora_periodo_letivo = false;
    $enrollment->schoolClass->school->institution->save();

    $stage = $this->schoolClass->stages()->first();

    $stage->data_inicio = now()->addDay();
    $stage->save();

    $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );
});

/**
 * Permite matrícular antes do início do ano letivo se o parâmetro permitir_matricula_fora_periodo_letivo
 * estiver habilitado na instituição
 *
 * @return void
 *
 * @throws Throwable
 */
test('enroll date before academic year allowed', function () {
    $enrollment = LegacyEnrollmentFactory::new()->make([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $enrollment->schoolClass->school->institution->permitir_matricula_fora_periodo_letivo = true;
    $enrollment->schoolClass->school->institution->save();

    $stage = $this->schoolClass->stages()->first();

    $stage->data_inicio = now()->addDay();
    $stage->save();

    $enrollment = $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );

    $this->assertInstanceOf(LegacyEnrollment::class, $enrollment);
});

/**
 * Erro ao enturmar uma matrícula devido a data de entrada ser posterior ao
 * fim do ano letivo.
 *
 * @return void
 *
 * @throws Throwable
 */
test('enroll date after academic year exception', function () {
    $this->expectException(EnrollDateAfterAcademicYearException::class);

    $enrollment = LegacyEnrollmentFactory::new()->make([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $stage = $this->schoolClass->stages()->first();

    $stage->data_fim = now()->subDay();
    $stage->save();

    $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()
    );
});

/**
 * A data de enturmação é anterior a data de matrícula.
 *
 * @return void
 *
 * @throws Throwable
 */
test('previous enroll date exception', function () {
    $this->expectException(PreviousEnrollDateException::class);

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $this->service->cancelEnrollment($enrollment, now());

    $this->service->enroll(
        $enrollment->registration,
        $enrollment->schoolClass,
        now()->subDay(1)
    );
});

/**
 * Instituição sem data base, a ultima enturmação deverá ser retornada
 */
test('get previous enrollment withou relocation date', function () {
    /** @var LegacyEnrollment $enrollment */
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
    ]);

    $enrollment->schoolClass->school->institution->data_base_remanejamento = null;
    $enrollment->schoolClass->school->institution->save();

    $lastEnrollment = $this->service->getPreviousEnrollmentAccordingToRelocationDate($enrollment->registration);

    $this->assertEquals($enrollment->id, $lastEnrollment->id);
});

/**
 * Instituição com data base antes da data de remanejamento, devera retornar null
 */
test('get previous enrollment with relocation date before departed date', function () {
    /** @var LegacyEnrollment $enrollment */
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
        'data_exclusao' => now(),
    ]);

    $enrollment->schoolClass->school->institution->data_base_remanejamento = Carbon::yesterday();
    $enrollment->schoolClass->school->institution->save();

    $lastEnrollment = $this->service->getPreviousEnrollmentAccordingToRelocationDate($enrollment->registration);

    $this->assertEquals($enrollment->id, $lastEnrollment->id);
});

/**
 * Instituição com data base depois da data de remanejamento, a ultima enturmação deverá ser retornada
 */
test('get previous enrollment with relocation date after departed date', function () {
    /** @var LegacyEnrollment $enrollment */
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
        'data_exclusao' => Carbon::yesterday(),
    ]);

    $enrollment->schoolClass->school->institution->data_base_remanejamento = now();
    $enrollment->schoolClass->school->institution->save();

    $lastEnrollment = $this->service->getPreviousEnrollmentAccordingToRelocationDate($enrollment->registration);

    $this->assertNull($lastEnrollment);
});

test('reorder', function () {
    $schoolClass = $this->schoolClass->getKey();

    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    $registration = $enrollment->registration->getKey();

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
        'sequencial' => 3,
    ]);

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
        'sequencial' => 5,
    ]);

    $this->service->reorder($enrollment->registration);

    $this->assertDatabaseHas('pmieducar.matricula_turma', [
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
        'sequencial' => 2,
    ]);
    $this->assertDatabaseHas('pmieducar.matricula_turma', [
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
        'sequencial' => 3,
    ]);
    $this->assertDatabaseMissing('pmieducar.matricula_turma', [
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
        'sequencial' => 5,
    ]);
});

test('find find all is enrolled and another classroom enrollments', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'ativo' => 1]);
    $elsewhere = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $enrollment->registration, 'ativo' => 0]);

    $this->assertSame($enrollment->id, $this->service->find($enrollment->id)->id);
    $this->assertSame([$enrollment->id, $elsewhere->id], $this->service->findAll([$enrollment->id, $elsewhere->id])->sortBy('id')->pluck('id')->values()->all());
    $this->assertTrue($this->service->isEnrolled($this->schoolClass, $enrollment->registration));
    $this->assertFalse($this->service->isEnrolled($elsewhere->schoolClass, $enrollment->registration));

    $others = $this->service->anotherClassroomEnrollments($this->schoolClass, $enrollment->registration);

    $this->assertSame([$elsewhere->id], $others->pluck('id')->all());
    $this->assertTrue($others->first()->relationLoaded('schoolClass'));
});

test('cancel before the registration date exception', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->schoolClass,
        'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['data_matricula' => now()->addDays(2)]),
        'data_enturmacao' => now(),
    ]);

    $this->expectException(PreviousEnrollCancellationDateException::class);

    $this->service->cancelEnrollment($enrollment, now()->addDay());
});

test('enroll before the registration date exception', function () {
    $registration = LegacyRegistrationFactory::new()->create(['data_matricula' => now()->addDays(2)]);

    $this->expectException(PreviousEnrollRegistrationDateException::class);

    $this->service->enroll($registration, $this->schoolClass, now());
});

test('create from screen', function () {
    $this->assertNull($this->service->createFromScreen('x', $this->schoolClass->getKey(), 1));

    $registration = LegacyRegistrationFactory::new()->create();
    $unified = LegacySchoolClassFactory::new()->create();
    $period = LegacyPeriodFactory::new()->create();

    $enrollment = $this->service->createFromScreen(
        $registration->getKey(),
        $this->schoolClass->getKey(),
        1,
        now()->format('Y-m-d'),
        censusStage: 14,
        unifiedSchoolClass: $unified->getKey(),
        periodId: $period->getKey(),
    );

    $this->assertSame(1, $enrollment->sequencial);
    $this->assertSame(14, $enrollment->etapa_educacenso);
    $this->assertSame($unified->getKey(), $enrollment->turma_unificada);
    $this->assertSame($period->getKey(), $enrollment->turno_id);
    $this->assertSame(now()->format('Y-m-d'), $enrollment->data_enturmacao->format('Y-m-d'));
});

test('update from screen', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'sequencial' => 1, 'ativo' => 1]);
    $registration = $enrollment->ref_cod_matricula;
    $other = LegacySchoolClassFactory::new()->create();
    $period = LegacyPeriodFactory::new()->create();

    $this->assertFalse($this->service->updateFromScreen($registration, $this->schoolClass->getKey(), 1, []));
    $this->assertTrue($this->service->updateFromScreen($registration, $this->schoolClass->getKey(), 99, ['ref_usuario_exc' => 1]));

    $this->assertTrue($this->service->updateFromScreen($registration, $this->schoolClass->getKey(), 1, [
        'ref_usuario_exc' => 1,
        'ref_usuario_cad' => 1,
        'data_cadastro' => '2030-01-01 08:00:00',
        'data_exclusao' => '2030-05-01',
        'etapa_educacenso' => 14,
        'turma_unificada' => $other->getKey(),
        'ativo' => 0,
        'transferido' => true,
        'ref_cod_turma_transf' => $other->getKey(),
        'data_enturmacao' => '2030-02-02',
        'turno_id' => (string) $period->getKey(),
        'tipo_atendimento' => '1,2',
    ]));

    $enrollment->refresh();

    $this->assertSame(0, $enrollment->ativo);
    $this->assertTrue($enrollment->transferido);
    $this->assertSame('2030-05-01', $enrollment->data_exclusao->format('Y-m-d'));
    $this->assertSame('2030-02-02', $enrollment->data_enturmacao->format('Y-m-d'));
    $this->assertSame(14, $enrollment->etapa_educacenso);
    $this->assertSame($other->getKey(), $enrollment->turma_unificada);
    $this->assertSame($other->getKey(), $enrollment->ref_cod_turma);
    $this->assertSame($period->getKey(), $enrollment->turno_id);
    $this->assertSame('{1,2}', $enrollment->getRawOriginal('tipo_atendimento'));

    $this->assertTrue($this->service->updateFromScreen($registration, $other->getKey(), 1, [
        'ref_usuario_exc' => 1,
        'etapa_educacenso' => 0,
        'turno_id' => '0',
        'tipo_atendimento' => null,
        'ativo' => 1,
    ]));

    $enrollment->refresh();

    $this->assertNull($enrollment->etapa_educacenso);
    $this->assertNull($enrollment->turno_id);
    $this->assertNull($enrollment->getRawOriginal('tipo_atendimento'));
    $this->assertFalse($enrollment->transferido);
    $this->assertSame(1, $enrollment->ativo);
});

test('register departures ignore invalid ids and reclassification closes everything', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_turma' => $this->schoolClass, 'sequencial' => 1, 'ativo' => 1]);

    $this->service->registerTransfer('x', 1);
    $this->service->registerAbandonment($registration->getKey(), 'x');
    $this->service->registerDeath(null, 1);

    $this->assertFalse($this->service->registerReclassification('x'));
    $this->assertFalse((bool) $enrollment->refresh()->transferido);
    $this->assertSame(1, $enrollment->ativo);

    $this->assertTrue($this->service->registerReclassification($registration->getKey(), '2030-06-30'));

    $enrollment->refresh();

    $this->assertTrue($enrollment->reclassificado);
    $this->assertSame(0, $enrollment->ativo);
    $this->assertSame('2030-06-30', $enrollment->data_exclusao->format('Y-m-d'));
});

test('marks each situation', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass]);

    $this->service->markAsTransferred($enrollment);
    $this->assertTrue($enrollment->refresh()->transferido);

    $this->service->markAsRelocated($enrollment);
    $this->assertTrue($enrollment->refresh()->remanejado);

    $this->service->markAsRelocatedSameClassGroup($enrollment);
    $this->assertTrue($enrollment->refresh()->remanejado_mesma_turma);

    $this->service->markAsReclassified($enrollment);
    $this->assertTrue($enrollment->refresh()->reclassificado);

    $this->service->markAsAbandoned($enrollment);
    $this->assertTrue($enrollment->refresh()->abandono);

    $this->service->markAsDeceased($enrollment);
    $this->assertTrue($enrollment->refresh()->falecido);

    $this->service->markWithSituation($enrollment, 'abandono');
    $enrollment->refresh();
    $this->assertTrue($enrollment->abandono);
    $this->assertFalse($enrollment->transferido);
    $this->assertFalse($enrollment->falecido);
});

test('previous enrollment is null without enrollments', function () {
    $this->assertNull($this->service->getPreviousEnrollmentAccordingToRelocationDate(LegacyRegistrationFactory::new()->create()));
});

test('reorder school class renumbers the valid enrollments and sends the given one to the end', function () {
    $schoolClass = $this->schoolClass->getKey();
    $first = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ativo' => 1, 'sequencial_fechamento' => 1]);
    $second = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ativo' => 1, 'sequencial_fechamento' => 2]);
    $third = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ativo' => 1, 'sequencial_fechamento' => 3]);
    $unnumbered = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ativo' => 1, 'sequencial_fechamento' => 0]);

    $this->service->reorderSchoolClass($unnumbered);
    $this->assertSame(1, $first->refresh()->sequencial_fechamento);

    $this->service->reorderSchoolClassAccordingToRelocationDate($second);

    // A enturmação sem número entra na contagem e empurra as outras.
    $this->assertSame(1, $unnumbered->refresh()->sequencial_fechamento);
    $this->assertSame(2, $first->refresh()->sequencial_fechamento);
    $this->assertSame(9999, $second->refresh()->sequencial_fechamento);
    $this->assertSame(4, $third->refresh()->sequencial_fechamento);
});

test('update exit date', function () {
    // Sem URL do iDiário, a regra de saída não consulta o serviço.
    LegacyGeneralConfigurationFactory::new()->create(['url_novo_educacao' => null, 'token_novo_educacao' => null]);
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass]);

    $this->service->updateExitDate($enrollment, new DateTime('2030-06-30'));

    $this->assertSame('2030-06-30', $enrollment->refresh()->data_exclusao->format('Y-m-d'));
});

/**
 * Com os campos do Censo obrigatórios, o aluno não pode estar em duas
 * turmas presenciais no mesmo horário.
 */
test('exists active enrollment same time exception', function () {
    $alfa = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1, 'nao_informar_educacenso' => 0]);
    $alfa->school->institution->update(['obrigar_campos_censo' => true]);
    // A mesma escola e série da primeira turma, para não quebrar a chave composta.
    $beta = LegacySchoolClassFactory::new()->morning()->create([
        'ref_ref_cod_escola' => $alfa->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $alfa->ref_ref_cod_serie,
        'ref_cod_curso' => $alfa->ref_cod_curso,
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
    ]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $alfa]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $beta]);
    $registration = LegacyRegistrationFactory::new()->create([
        'ano' => $alfa->ano,
        'aprovado' => 3,
        'ativo' => 1,
        'data_matricula' => now()->subMonth(),
    ]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_turma' => $alfa, 'ativo' => 1, 'data_enturmacao' => now()->subMonth()]);

    $this->expectException(ExistsActiveEnrollmentSameTimeException::class);

    $this->service->enroll($registration, $beta->fresh(), now());
});
