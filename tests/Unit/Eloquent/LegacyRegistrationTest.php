<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineDependence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyGrade;
use App\Models\LegacyIndividual;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentAbsence;
use App\Models\LegacyStudentDescriptiveOpinion;
use App\Models\LegacyStudentScore;
use App\Models\LegacyTransferRequest;
use App\Models\RegistrationStatus;
use App\Models\View\Situation;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRegistration::class, relations: [
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
    'transferStart' => LegacyTransferRequest::class,
    'activeLookings' => LegacyActiveLooking::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->cod_matricula, $this->model->id);
    $this->assertEquals($this->model->bloquear_troca_de_situacao, $this->model->isLockedToChangeStatus());
    $this->assertEquals($this->model->dependencia, $this->model->isDependency);
    $this->assertEquals($this->model->ano, $this->model->year);
    $this->assertEquals($this->model->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO, $this->model->isTransferred);
    $this->assertEquals($this->model->aprovado == App_Model_MatriculaSituacao::ABANDONO, $this->model->isAbandoned);
    $this->assertEquals($this->model->ativo === 0, $this->model->isCanceled);
    $this->assertEquals((new RegistrationStatus)->getDescriptiveValues()[(int) $this->model->aprovado], $this->model->statusDescription);
});

test('scope active', function () {
    LegacyRegistrationFactory::new()->create(['ativo' => 0]);
    $found = $this->instanceNewEloquentModel()->newQuery()->active()->get();

    $this->assertCount(1, $found);
});

test('get evaluation rule', function () {
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
});

test('scope male', function () {
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
});

test('scope female', function () {
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
});

test('last year', function () {
    LegacyRegistrationFactory::new()->create([
        'ano' => date('Y') - 1,
    ]);

    $found = $this->instanceNewEloquentModel()->lastYear()->get();

    $this->assertCount(1, $found);
});

test('current year', function () {
    $found = $this->instanceNewEloquentModel()->currentYear()->get();

    $this->assertCount(1, $found);
});

test('belongs to one lazy loading', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create();

    $this->assertInstanceOf(LegacySchoolClass::class, $registration->schoolClass);
    $this->assertEquals($schoolClass->cod_turma, $registration->schoolClass->cod_turma);
});

test('belongs to one eager loading', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create();

    $found = LegacyRegistration::with('schoolClass')->find($registration->cod_matricula);

    $this->assertInstanceOf(LegacySchoolClass::class, $found->schoolClass);
    $this->assertEquals($schoolClass->cod_turma, $found->schoolClass->cod_turma);
});

test('belongs to one returns null without enrollment', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $this->assertNull($registration->schoolClass);
});

test('school classes situations and discipline scores', function () {
    $this->seed(DefaultRelatorioSituacaoMatriculaTableSeeder::class);

    $registration = LegacyRegistrationFactory::new()->create(['aprovado' => RegistrationStatus::APPROVED, 'ativo' => 1]);
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);

    $this->assertSame([$enrollment->ref_cod_turma], $registration->schoolClasses->modelKeys());
    $this->assertInstanceOf(Situation::class, $registration->situations->first());
    $this->assertInstanceOf(Situation::class, $registration->situation);
    $this->assertInstanceOf(Situation::class, $registration->situationApproved);
    $this->assertSame(RegistrationStatus::APPROVED, $registration->situationApproved->cod_situacao);
    $this->assertTrue($registration->disciplineScores->isEmpty());
});

test('get evaluation rule prefers the differentiated rule of the school', function () {
    $gradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create(['ano_letivo' => 2030]);
    $registration = LegacyRegistrationFactory::new()->create([
        'ano' => 2030,
        'ref_ref_cod_serie' => $gradeYear->serie_id,
        'ref_ref_cod_escola' => LegacySchoolFactory::new()->create(['utiliza_regra_diferenciada' => true]),
    ]);

    $this->assertSame($gradeYear->regra_avaliacao_diferenciada_id, $registration->getEvaluationRule()->getKey());
});

test('filters', function () {
    $this->seed(DefaultRelatorioSituacaoMatriculaTableSeeder::class);

    $institution = LegacyInstitutionFactory::new()->create();
    $school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $institution, 'zona_localizacao' => 2, 'localizacao_diferenciada' => 3]);
    $student = fn (string $name) => LegacyStudentFactory::new()->create([
        'ref_idpes' => LegacyIndividualFactory::new()->create(['idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F'])]),
    ]);
    $anaStudent = $student('Ana Alves');
    $ana = LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $anaStudent,
        'ref_ref_cod_escola' => $school,
        'aprovado' => RegistrationStatus::APPROVED,
        'ultima_matricula' => 1,
        'data_matricula' => '2030-02-01',
        'dependencia' => true,
        'ativo' => 1,
    ]);
    $bia = LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $student('Bia Borges'),
        'aprovado' => RegistrationStatus::RECLASSIFIED,
        'ultima_matricula' => 0,
        'data_matricula' => '2030-03-01',
        'dependencia' => false,
        'ativo' => 1,
    ]);
    $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $ana, 'ativo' => 1]);
    $anaStudent->individual->races()->attach($race = LegacyRaceFactory::new()->create());
    $anaStudent->person->deficiencies()->attach(LegacyDeficiencyFactory::new()->create(['deficiency_type_id' => 5]));

    $ids = fn ($query) => $query->whereIn('cod_matricula', [$ana->getKey(), $bia->getKey()])->orderBy('cod_matricula')->pluck('cod_matricula')->all();
    $onlyAna = [$ana->getKey()];
    $onlyBia = [$bia->getKey()];
    $both = [$ana->getKey(), $bia->getKey()];

    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->finalized()));
    $this->assertSame($onlyBia, $ids(LegacyRegistration::query()->notFinalized()));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->validStatus()));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereLastRegistration()));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereName('alves')));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereInstitution($institution->getKey())));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereDeficiencyTypes('5,6')));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereRaceId($race->getKey())));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereSituation(RegistrationStatus::APPROVED)));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereSchoolClass($enrollment->ref_cod_turma)));
    $this->assertSame($onlyBia, $ids(LegacyRegistration::query()->whereRegistration($bia->getKey())));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->notReclassified()));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->allRelationsActive()));
    $this->assertSame($onlyBia, $ids(LegacyRegistration::query()->whereStartAfter('2030-02-15')));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereEndBefore('2030-02-15')));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereDependency(1)));
    $this->assertSame($onlyBia, $ids(LegacyRegistration::query()->whereDependency(2)));
    $this->assertSame($both, $ids(LegacyRegistration::query()->whereDependency(3)));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereLocalizationZone(2)));
    $this->assertSame($onlyAna, $ids(LegacyRegistration::query()->whereDifferentiatedLocalizationArea(3)));
});
