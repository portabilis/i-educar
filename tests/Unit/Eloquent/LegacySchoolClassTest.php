<?php

namespace Tests\Unit\Eloquent;

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
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
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
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolClass::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_turma',
            'name' => 'nm_turma',
            'year' => 'ano',
        ];
    }

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
    public function testGetTotalEnrolledMethod()
    {
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
    }

    /** @test */
    public function attributes()
    {
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
    }

    /** @test */
    public function relationshipGrades()
    {
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
    }

    /** @test */
    public function relationshipStages()
    {
        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->model->ref_ref_cod_escola,
            'ano' => $this->model->ano,
        ]);
        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $this->model,
        ]);
        $this->assertCount(1, $this->model->stages);
    }

    /** @test */
    public function disciplines()
    {
        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'turma_id' => $this->model,
        ]);
        $this->assertCount(1, $this->model->disciplines);
    }

    /** @test */
    public function getEvaluationRule(): void
    {
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
    }

    /** @test */
    public function getDisciplines(): void
    {
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
    }
}
