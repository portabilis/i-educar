<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyGrade;
use App\Models\LegacyPeriod;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\SchoolClassInep;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacySchoolClass::class,
    relations: [
        'course' => LegacyCourse::class,
        'grade' => LegacyGrade::class,
        'school' => LegacySchool::class,
        'period' => LegacyPeriod::class,
        'enrollments' => LegacyEnrollment::class,
        'schoolClassStages' => LegacySchoolClassStage::class,
        'multigrades' => LegacySchoolClassGrade::class,
        'academicYears' => LegacySchoolAcademicYear::class,
        'schoolGrade' => LegacySchoolGrade::class,
        'inep' => SchoolClassInep::class,
    ],
    legacyAttributes: [
        'id' => 'cod_turma',
        'name' => 'nm_turma',
        'year' => 'ano',
    ],
));

EloquentModelTestCase::registerModelTests();

/**
 * Serão cadastradas:
 *
 * - 1 enturmação ativa sendo matrícula de depêndencia.
 * - 1 enturmação inativa.
 * - 1 enturmação ativa.
 *
 * O total deve contabilizar:
 *
 * - Apenas enturmações ativas.
 * - Matrículas que não sejam de dependências.
 *
 * @return void
 */
test('get total enrolled method', function () {
    /** @var LegacySchoolClass $schoolClass */
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()->create([
        'dependencia' => true,
    ]);
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
        'ref_cod_matricula' => $registration,
    ]);
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
        'ativo' => false,
    ]);
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass,
    ]);
    $this->assertEquals(1, $schoolClass->getTotalEnrolled());
});

test('attributes', function () {
    $this->assertEquals($this->model->visivel, $this->model->visible);
    $this->assertEquals($this->model->ref_cod_disciplina_dispensada, $this->model->exemptedDisciplineId);
    $this->assertInstanceOf(Collection::class, $this->model->getActiveEnrollments());
    $this->model->schoolGrade = null;
    $this->assertEquals(true, $this->model->denyEnrollmentsWhenNoVacancy());
    $this->model->hora_inicial = null;
    $this->assertEquals(0, $this->model->getClassTime());
    $this->assertEquals($this->model->cod_turma, $this->model->id);

    if (empty($this->model->year)) {
        $expected = $this->model->nm_turma;
    } else {
        $expected = $this->model->nm_turma . ' (' . $this->model->year . ')';
    }
    $this->assertEquals($expected, $this->model->name);
    $this->assertEquals($this->model->ano, $this->model->year);
    $this->assertEquals($this->model->ref_ref_cod_escola, $this->model->schoolId);
    $this->assertEquals($this->model->ref_cod_curso, $this->model->courseId);
    $this->assertEquals($this->model->ref_ref_cod_serie, $this->model->gradeId);

    $vacancies = $this->model->max_aluno - $this->model->getTotalEnrolled();
    $expected = max($vacancies, 0);
    $this->assertEquals($expected, $this->model->vacancies);

    $expected = $this->model->stages()->orderBy('sequencial')->value('data_inicio');
    $this->assertEquals($expected, $this->model->beginAcademicYearAttribute);
    $expected = $this->model->stages()->orderByDesc('sequencial')->value('data_fim');
    $this->assertEquals($expected, $this->model->endAcademicYearAttribute);
    $this->assertEquals('Seg à Sex', $this->model->daysOfWeekName);
    $this->model->dias_semana = [1, 3, 5];
    $this->assertEquals('Dom, Ter, Qui', $this->model->daysOfWeekName);
});

test('relationship grades', function () {
    $grade = LegacyGradeFactory::new()->create();
    $school = LegacySchoolFactory::new()->create();
    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $school,
        'ref_cod_serie' => $grade,
    ]);
    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $school,
        'serie_id' => $grade,
        'turma_id' => $this->model,
    ]);
    $this->assertCount(1, $this->model->grades);
    $this->assertInstanceOf(LegacyGrade::class, $this->model->grades->first());
});

test('relationship stages', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->model->ref_ref_cod_escola,
        'ano' => $this->model->ano,
    ]);
    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->model,
    ]);
    $this->assertCount(1, $this->model->stages);
});

test('disciplines', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $discipline,
        'turma_id' => $this->model,
    ]);
    $this->assertCount(1, $this->model->disciplines);
});

test('get evaluation rule', function () {
    $evaluationRuleGradeYear = $this->model
        ->hasOne(LegacyEvaluationRuleGradeYear::class, 'serie_id', 'ref_ref_cod_serie')
        ->where('ano_letivo', $this->model->ano)
        ->firstOrFail();
    if ($this->model->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
        $expected = $evaluationRuleGradeYear->differentiatedEvaluationRule;
    } else {
        $expected = $evaluationRuleGradeYear->evaluationRule;
    }
    $this->assertEquals($expected, $this->model->getEvaluationRule());
});

test('get disciplines', function () {
    if ($this->model->multiseriada) {
        $multigrades = $this->multigrades->pluck('serie_id')->toArray();
        $expected = LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_escola', $this->model->school_id)
            ->whereIn('ref_ref_cod_serie', $multigrades)
            ->whereRaw('? = ANY(anos_letivos)', [$this->model->year])
            ->get()
            ->map(fn ($schoolGrade) => $schoolGrade->discipline);
    } else {
        $disciplinesOfSchoolClass = $this->model->disciplines()->get();
        if ($disciplinesOfSchoolClass->count() > 0) {
            $expected = $disciplinesOfSchoolClass;
        } else {
            $expected = LegacySchoolGradeDiscipline::query()
                ->where('ref_ref_cod_escola', $this->model->school_id)
                ->where('ref_ref_cod_serie', $this->model->grade_id)
                ->whereRaw('? = ANY(anos_letivos)', [$this->model->year])
                ->get()
                ->map(fn ($schoolGrade) => $schoolGrade->model->discipline);
        }
    }
    $this->assertEquals($expected, $this->model->getDisciplines());
});

test('regent and teachers relations', function () {
    LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $this->model]);

    $this->assertInstanceOf(BelongsTo::class, $this->model->regentPerson());
    $this->assertCount(1, $this->model->schoolClassTeachers);
});

test('get stages by calendar', function () {
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $this->model]);

    $this->assertCount(1, $this->model->getStages(false));
    $this->assertCount(0, $this->model->getStages(true));
});

test('active enrollments with cancellable registration', function () {
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->model,
        'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['ano' => $this->model->ano, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO]),
    ]);
    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $this->model,
        'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['ano' => $this->model->ano, 'aprovado' => App_Model_MatriculaSituacao::APROVADO]),
    ]);

    $this->assertCount(1, $this->model->getActiveEnrollmentsWithCancellableRegistration());
});

test('deny enrollments when no vacancy follows the school grade', function () {
    $this->model->schoolGrade->update(['bloquear_enturmacao_sem_vagas' => 1]);

    $this->assertTrue($this->model->denyEnrollmentsWhenNoVacancy());
});

test('class time in hours', function () {
    $this->model->hora_inicial = '08:00';
    $this->model->hora_final = '12:30';

    $this->assertEquals(4, $this->model->getClassTime());
});

test('grade disciplines', function () {
    LegacyDisciplineAcademicYearFactory::new()->create(['ano_escolar_id' => $this->model->ref_ref_cod_serie]);

    $this->assertCount(1, $this->model->gradeDisciplines);
});

test('get disciplines falls back to the school grade and reads all grades of a multigrade class', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $this->model->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $this->model->ref_ref_cod_serie,
        'ref_cod_disciplina' => $discipline,
        'anos_letivos' => '{' . $this->model->ano . '}',
    ]);

    $this->assertEquals([$discipline->getKey()], $this->model->getDisciplines()->pluck('id')->all());

    $own = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineSchoolClassFactory::new()->create(['componente_curricular_id' => $own, 'turma_id' => $this->model]);

    $this->assertEquals([$own->getKey()], $this->model->getDisciplines()->pluck('id')->all());

    $this->model->multiseriada = 1;
    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $this->model->ref_ref_cod_escola,
        'serie_id' => $this->model->ref_ref_cod_serie,
        'turma_id' => $this->model,
    ]);

    $this->assertEquals([$discipline->getKey()], $this->model->getDisciplines()->pluck('id')->all());
});

test('get evaluation rule differentiated by the school', function () {
    $differentiated = LegacyEvaluationRuleFactory::new()->create();
    $this->model->school->update(['utiliza_regra_diferenciada' => true]);
    LegacyEvaluationRuleGradeYear::query()
        ->where('serie_id', $this->model->ref_ref_cod_serie)
        ->where('ano_letivo', $this->model->ano)
        ->update(['regra_avaliacao_diferenciada_id' => $differentiated->getKey()]);

    $this->assertEquals($differentiated->getKey(), $this->model->getEvaluationRule()->getKey());
});

test('allows integrated workload by curricular organization or stage', function () {
    $this->model->organizacao_curricular = null;
    $this->model->etapa_educacenso = 14;
    $this->assertFalse($this->model->permiteCargaHorariaIntegralizada());

    $this->model->etapa_educacenso = 39;
    $this->assertTrue($this->model->permiteCargaHorariaIntegralizada());

    $this->model->etapa_educacenso = 14;
    $this->model->organizacao_curricular = '{5}';
    $this->assertTrue($this->model->permiteCargaHorariaIntegralizada());
});
