<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Services\SchoolClass\AvailableTimeService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStudentFactory;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    /**
     * Uma turma de outra turma com horários e matrícula ativa do aluno no
     * mesmo ano, para o conflito de horário.
     */
    $this->conflictingPair = function (array $attributes = [], array $otherAttributes = [], array $enrollment = []): array {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create($attributes + ['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create($otherAttributes + ['tipo_mediacao_didatico_pedagogico' => 1, 'ano' => $schoolClass->ano]);
        $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano, 'aprovado' => 3]);

        LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);
        LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $otherSchoolClass]);

        LegacyEnrollmentFactory::new()->active()->create($enrollment + [
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        return [$schoolClass, $otherSchoolClass, $registration];
    };

    $this->service = app(AvailableTimeService::class);
    $this->disableForeignKeys();
});

afterEach(function () {
    $this->enableForeignKeys();
});

/**
 * @return void
 */
test('without others enrollments returns true', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create();

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('with enrollments same day different time returns true', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->afternoon()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $otherSchoolClass,
    ]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('with enrollments same day same time same year returns false', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano, 'aprovado' => 3]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $otherSchoolClass,
    ]);

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('with enrollments same day same time different year returns true', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create(['ano' => ($schoolClass->ano - 1)]);

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('with enrollments different day same time returns false', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create([
        'tipo_mediacao_didatico_pedagogico' => 1,
        'dias_semana' => '{1, 7}',
    ]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create();

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('should launch exception when pass invalid school class id', function () {
    $this->expectException(ModelNotFoundException::class);

    $registration = LegacyRegistrationFactory::new()->create();

    $this->service->isAvailable($registration->ref_cod_aluno, -1);
});

/**
 * @return void
 */
test('same year but different academic periods', function () {
    $student = LegacyStudentFactory::new()->create();

    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
        'data_inicio' => now()->subMonths(3),
        'data_fim' => now()->subMonths(2),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $otherSchoolClass,
        'data_inicio' => now()->addMonths(3),
        'data_fim' => now()->addMonths(2),
    ]);

    $registration = LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $student,
    ]);

    $otherRegistration = LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $student,
    ]);

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
    ]);

    $this->assertTrue($this->service->isAvailable($otherRegistration->ref_cod_aluno, $otherSchoolClass->cod_turma));
});

/**
 * @return void
 */
test('with enrollments same day same time same year and one aee other escolarizacao returns true', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(
        [
            'tipo_mediacao_didatico_pedagogico' => 1,
            'tipo_atendimento' => '{' . TipoAtendimentoTurma::AEE . '}',
        ]
    );
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(
        [
            'tipo_mediacao_didatico_pedagogico' => 1,
            'tipo_atendimento' => '{' . TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO . '}',
            'hora_final' => '15:45',
        ]
    );

    $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $otherSchoolClass,
    ]);

    LegacyEnrollmentFactory::new()->active()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

/**
 * @return void
 */
test('with inactive enrollments same day same time same year returns true', function () {
    $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
    $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano, 'aprovado' => 3]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $otherSchoolClass,
    ]);

    LegacyEnrollmentFactory::new()->inactive()->create([
        'ref_cod_turma' => $otherSchoolClass->cod_turma,
        'ref_cod_matricula' => $registration->cod_matricula,
    ]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('turma que nao e presencial esta sempre disponivel', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)(['tipo_mediacao_didatico_pedagogico' => 2]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('turma fora do censo esta disponivel quando so as do censo contam', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)(['nao_informar_educacenso' => 1]);

    $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    $this->assertTrue($this->service->onlySchoolClassesInformedOnCensus()->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('outra turma fora do censo nao conflita quando so as do censo contam', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)([], ['nao_informar_educacenso' => 1]);

    $this->assertTrue($this->service->onlySchoolClassesInformedOnCensus()->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('so as enturmacoes anteriores a data contam', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)([], [], ['data_enturmacao' => '2030-03-10']);

    $this->assertFalse($this->service->onlyUntilEnrollmentDate(new DateTime('2030-03-11'))->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    $this->assertTrue($this->service->onlyUntilEnrollmentDate(new DateTime('2030-03-10'))->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('outra turma nao presencial nao conflita', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)([], ['tipo_mediacao_didatico_pedagogico' => 3]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('turma sem dias da semana nao conflita', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)(['dias_semana' => null]);

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('turma sem etapas nao conflita', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)();
    $schoolClass->schoolClassStages()->delete();

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, LegacySchoolClass::query()->find($schoolClass->cod_turma)->cod_turma));
});

test('turma integral compara o horario do turno da enturmacao', function () {
    // O turno integral é o de código fixo 4.
    $fulltime = LegacyPeriod::query()->find(Period::FULLTIME) ?? LegacyPeriodFactory::new()->create(['id' => Period::FULLTIME, 'nome' => 'Integral']);
    $hours = ['hora_inicial_matutino' => '07:00', 'hora_final_matutino' => '11:00', 'hora_inicial_vespertino' => '13:00', 'hora_final_vespertino' => '17:00'];
    $otherHours = ['hora_inicial_matutino' => '08:00', 'hora_final_matutino' => '12:00', 'hora_inicial_vespertino' => '13:30', 'hora_final_vespertino' => '17:30'];

    [$schoolClass, , $registration] = ($this->conflictingPair)(
        $hours + ['turma_turno_id' => $fulltime, 'hora_inicial' => '07:00', 'hora_final' => '17:00'],
        $otherHours + ['turma_turno_id' => $fulltime, 'hora_inicial' => '08:00', 'hora_final' => '17:30'],
        ['turno_id' => Period::MORNING]
    );

    // De manhã os dois horários matutinos se cruzam; à tarde a outra
    // enturmação é de manhã, então não há conflito.
    $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma, Period::MORNING));
    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma, Period::AFTERNOON));
    $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));

    LegacyEnrollment::query()->where('ref_cod_matricula', $registration->cod_matricula)->update(['turno_id' => Period::AFTERNOON]);

    $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma, Period::AFTERNOON));
    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma, Period::MORNING));
});

test('turma de escolarizacao nao conflita com turma de aee', function () {
    [$schoolClass, , $registration] = ($this->conflictingPair)(
        ['tipo_atendimento' => '{' . TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO . '}'],
        ['tipo_atendimento' => '{' . TipoAtendimentoTurma::AEE . '}'],
    );

    $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
});

test('turma sem etapa nao tem datas', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->assertFalse($this->service->hasDates($schoolClass));

    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);

    $this->assertTrue($this->service->hasDates(LegacySchoolClass::query()->find($schoolClass->cod_turma)));
});

test('turma com inicio mas sem fim do ano letivo nao tem datas', function () {
    $schoolClass = Mockery::mock(LegacySchoolClass::class)->makePartial();
    $schoolClass->shouldReceive('getAttribute')->with('begin_academic_year')->andReturn(now());
    $schoolClass->shouldReceive('getAttribute')->with('end_academic_year')->andReturn(null);

    $this->assertFalse($this->service->hasDates($schoolClass));
});
