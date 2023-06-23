<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineDependence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyGrade;
use App\Models\LegacyIndividual;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentAbsence;
use App\Models\LegacyStudentDescriptiveOpinion;
use App\Models\LegacyStudentScore;
use App\Models\RegistrationStatus;
use App_Model_MatriculaSituacao;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentTestCase;

class LegacyRegistrationTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'student' => LegacyStudent::class,
        'school' => LegacySchool::class,
        'grade' => LegacyGrade::class,
        'course' => LegacyCourse::class,
        'enrollments' => LegacyEnrollment::class,
        'activeEnrollments' => LegacyEnrollment::class,
        'lastEnrollment' => LegacyEnrollment::class,
        'exemptions' => LegacyDisciplineExemption::class,
        'studentAbsence' => LegacyStudentAbsence::class,
        'studentScore' => LegacyStudentScore::class,
        'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
        'dependencies' => LegacyDisciplineDependence::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRegistration::class;
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->cod_matricula, $this->model->id);
        $this->assertEquals($this->model->bloquear_troca_de_situacao, $this->model->isLockedToChangeStatus());
        $this->assertEquals($this->model->dependencia, $this->model->isDependency);
        $this->assertEquals($this->model->ano, $this->model->year);
        $this->assertEquals($this->model->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO, $this->model->isTransferred);
        $this->assertEquals($this->model->aprovado == App_Model_MatriculaSituacao::ABANDONO, $this->model->isAbandoned);
        $this->assertEquals($this->model->ativo === 0, $this->model->isCanceled);
        $this->assertEquals((new RegistrationStatus())->getDescriptiveValues()[(int) $this->model->aprovado], $this->model->statusDescription);
    }

    public function testScopeActive(): void
    {
        LegacyRegistrationFactory::new()->create(['ativo' => 0]);
        $found = $this->instanceNewEloquentModel()->newQuery()->active()->get();

        $this->assertCount(1, $found);
    }

    public function testGetEvaluationRule()
    {
        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $this->model->ref_ref_cod_serie,
            'ano_letivo' => $this->model->ano,
        ]);

        $evaluationRuleGradeYear = $this->model->hasOne(LegacyEvaluationRuleGradeYear::class, 'serie_id', 'ref_ref_cod_serie')
            ->where('ano_letivo', $this->model->ano)
            ->firstOrFail();

        if ($this->model->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            $expected = $evaluationRuleGradeYear->differentiatedEvaluationRule;
        } else {
            $expected = $evaluationRuleGradeYear->evaluationRule;
        }

        $this->assertEquals($expected, $this->model->getEvaluationRule());
    }

    public function testScopeMale(): void
    {
        LegacyIndividual::query()->update([
            'sexo' => null,
        ]);

        $individual1 = LegacyIndividualFactory::new()->create([
            'sexo' => 'M',
        ]);
        $student1 = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual1,
        ]);

        LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student1,
        ]);

        $individual2 = LegacyIndividualFactory::new()->create([
            'sexo' => 'F',
        ]);
        $student2 = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual2,
        ]);
        LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student2,
        ]);

        $found = $this->instanceNewEloquentModel()->male()->get();
        $this->assertCount(1, $found);
    }

    public function testScopeFemale(): void
    {
        LegacyIndividual::query()->update([
            'sexo' => null,
        ]);

        $individual1 = LegacyIndividualFactory::new()->create([
            'sexo' => 'M',
        ]);
        $student1 = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual1,
        ]);

        LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student1,
        ]);

        $individual2 = LegacyIndividualFactory::new()->create([
            'sexo' => 'F',
        ]);
        $student2 = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual2,
        ]);
        LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student2,
        ]);

        $found = $this->instanceNewEloquentModel()->female()->get();
        $this->assertCount(1, $found);
    }

    public function testLastYear(): void
    {
        LegacyRegistrationFactory::new()->create([
            'ano' => date('Y') - 1,
        ]);

        $found = $this->instanceNewEloquentModel()->lastYear()->get();

        $this->assertCount(1, $found);
    }

    public function testCurrentYear(): void
    {
        $found = $this->instanceNewEloquentModel()->currentYear()->get();

        $this->assertCount(1, $found);
    }
}
