<?php

use App\Exceptions\AcademicYearServiceException;
use App\Models\EmployeeAllocation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyUser;
use App\Services\AcademicYearService;
use App\Services\iDiarioService;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralAbsenceFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->setupTestData = function (): void {
        $this->currentYear = now()->year;
        $this->previousYear = $this->currentYear - 1;

        $this->school = LegacySchoolFactory::new()->create();
        $this->course = LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);
        $this->grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $this->course->cod_curso,
        ]);

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ref_cod_curso' => $this->course->cod_curso,
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ref_cod_serie' => $this->grade->cod_serie,
        ]);

        $this->stageType = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
            'nm_tipo' => 'Bimestral',
            'num_etapas' => 4,
        ]);
    };

    $this->createUser = function (): LegacyUser {
        return LegacyUserFactory::new()->create();
    };

    $this->getDefaultDates = function (): array {
        return [
            'startDates' => ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear],
            'endDates' => ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear],
            'schoolDays' => [100, 100],
        ];
    };

    $this->createPreviousAcademicYear = function (): LegacySchoolAcademicYear {
        return LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->previousYear,
            'ativo' => 1,
        ]);
    };

    $this->createDefaultClass = function (?int $year = null): LegacySchoolClass {
        $year = $year ?? $this->previousYear;

        return LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->school->cod_escola,
            'ref_ref_cod_serie' => $this->grade->cod_serie,
            'ref_cod_curso' => $this->course->cod_curso,
            'ano' => $year,
            'nm_turma' => 'Turma A',
        ]);
    };

    $this->createAdditionalSchool = function (): LegacySchool {
        return LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);
    };

    $this->createCurrentAcademicYear = function (): LegacySchoolAcademicYear {
        return LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 1,
        ]);
    };

    $this->createTeacherWithDiscipline = function (LegacySchoolClass $class): array {
        $teacher = LegacySchoolClassTeacherFactory::new()->create([
            'turma_id' => $class->cod_turma,
            'ano' => $this->previousYear,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $teacher->id,
            'componente_curricular_id' => $discipline->id,
        ]);

        return ['teacher' => $teacher, 'discipline' => $discipline];
    };

    $this->createEmployeeWithRole = function (bool $isProfessor = false): array {
        $role = LegacyRoleFactory::new()->create(['professor' => $isProfessor ? 1 : 0]);
        $employee = EmployeeFactory::new()->create();
        $employeeRole = LegacyEmployeeRoleFactory::new()->create([
            'ref_cod_funcao' => $role->cod_funcao,
            'ref_cod_servidor' => $employee->cod_servidor,
            'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);

        return ['employee' => $employee, 'role' => $role, 'employeeRole' => $employeeRole];
    };

    $this->createEmployeeAllocation = function (array $employeeData, ?int $year = null): EmployeeAllocation {
        $year = $year ?? $this->previousYear;

        return EmployeeAllocationFactory::new()->create([
            'ano' => $year,
            'ref_cod_escola' => $this->school->cod_escola,
            'ref_cod_servidor' => $employeeData['employee']->cod_servidor,
            'ref_cod_servidor_funcao' => $employeeData['employeeRole']->cod_servidor_funcao,
            'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
            'ativo' => 1,
            'carga_horaria' => 40,
            'periodo' => 1,
            'hora_inicial' => '07:00:00',
            'hora_final' => '12:00:00',
            'dia_semana' => 2,
            'data_admissao' => '2020-01-01',
            'created_by' => 1,
            'deleted_by' => null,
        ]);
    };

    $this->setupBasicCopyTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();
        ($this->createDefaultClass)();
        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'dates' => $dates,
        ];
    };

    $this->setupTeacherCopyTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();
        $originalClass = ($this->createDefaultClass)();
        $teacherData = ($this->createTeacherWithDiscipline)($originalClass);
        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'teacherData' => $teacherData,
            'dates' => $dates,
        ];
    };

    $this->setupEmployeeCopyTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();
        ($this->createDefaultClass)();
        $employeeData = ($this->createEmployeeWithRole)(isProfessor: false);
        ($this->createEmployeeAllocation)($employeeData);
        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'employeeData' => $employeeData,
            'dates' => $dates,
        ];
    };

    $this->setupCompleteCopyTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();
        $originalClass = ($this->createDefaultClass)();
        $teacherData = ($this->createTeacherWithDiscipline)($originalClass);
        $employeeData = ($this->createEmployeeWithRole)(isProfessor: false);
        ($this->createEmployeeAllocation)($employeeData);
        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'teacherData' => $teacherData,
            'employeeData' => $employeeData,
            'dates' => $dates,
        ];
    };

    $this->setupMultigradeClassTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();

        $originalClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->school->cod_escola,
            'ref_ref_cod_serie' => $this->grade->cod_serie,
            'ref_cod_curso' => $this->course->cod_curso,
            'ano' => $this->previousYear,
            'nm_turma' => 'Turma Multisseriada',
            'multiseriada' => 1,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $this->school->cod_escola,
            'turma_id' => $originalClass->cod_turma,
            'serie_id' => $this->grade->cod_serie,
        ]);

        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'dates' => $dates,
        ];
    };

    $this->setupDisciplinesTest = function (): array {
        $user = ($this->createUser)();
        ($this->createPreviousAcademicYear)();
        $originalClass = ($this->createDefaultClass)();

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'turma_id' => $originalClass->cod_turma,
            'componente_curricular_id' => $discipline->id,
            'escola_id' => $this->school->cod_escola,
            'ano_escolar_id' => $this->grade->cod_serie,
        ]);

        $dates = ($this->getDefaultDates)();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'discipline' => $discipline,
            'dates' => $dates,
        ];
    };

    $this->batchActionParams = function (array $overrides = []): array {
        return $overrides + [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'user' => ($this->createUser)(),
        ];
    };

    $this->service = new AcademicYearService;

    ($this->setupTestData)();
});

test('create academic year for school creates academic year', function () {
    $user = ($this->createUser)();
    $dates = ($this->getDefaultDates)();

    $result = $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $dates['startDates'],
        endDates: $dates['endDates'],
        schoolDays: $dates['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    expect($result)->toBeInstanceOf(LegacySchoolAcademicYear::class);
    expect($result->ano)->toBe($this->currentYear);
    expect($result->ref_cod_escola)->toBe($this->school->cod_escola);
    expect($result->ativo)->toBe(1);
});

test('create academic year for school creates stages', function () {
    $user = ($this->createUser)();
    $dates = ($this->getDefaultDates)();

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $dates['startDates'],
        endDates: $dates['endDates'],
        schoolDays: $dates['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->get();

    expect($stages)->toHaveCount(2);
    expect($stages->first()->data_inicio->format('Y-m-d'))->toBe($this->currentYear . '-02-01');
    expect($stages->first()->data_fim->format('Y-m-d'))->toBe($this->currentYear . '-04-30');
    expect($stages->first()->dias_letivos)->toBe(100);
});

test('update academic year stages', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = [100, 100];

    $this->service->updateAcademicYearStages(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo
    );

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->get();

    expect($stages)->toHaveCount(2);
    expect($stages->first()->data_inicio->format('Y-m-d'))->toBe($this->currentYear . '-02-01');
    expect($stages->first()->data_fim->format('Y-m-d'))->toBe($this->currentYear . '-04-30');
    expect($stages->first()->dias_letivos)->toBe(100);
});

test('delete academic year returns true when successful', function () {
    $user = ($this->createUser)();
    $academicYear = LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'escola_ano_letivo_id' => $academicYear->id,
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => $this->currentYear,
    ]);

    $result = $this->service->deleteAcademicYear(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        userId: $user->getKey()
    );

    expect($result)->toBeTrue();

    $academicYear->refresh();
    expect($academicYear->ativo)->toBe(0);
    expect($academicYear->andamento)->toBe(2);
    expect($academicYear->ref_usuario_cad)->toBe($user->getKey());

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->count();

    expect($stages)->toBe(0);
});

test('validate stage count with module', function () {
    expect(fn () => $this->service->validateStageCountWithModule(
        moduleId: $this->stageType->cod_modulo,
        stagesCount: $this->stageType->num_etapas
    ))->not()->toThrow(RuntimeException::class);
});

test('validate stage count with module invalid count', function () {
    expect(fn () => $this->service->validateStageCountWithModule(
        moduleId: $this->stageType->cod_modulo,
        stagesCount: $this->stageType->num_etapas + 1
    ))->toThrow(AcademicYearServiceException::class);
});

test('validate stage count with nonexistent module', function () {
    expect(fn () => $this->service->validateStageCountWithModule(
        moduleId: 9999,
        stagesCount: 4
    ))->toThrow(AcademicYearServiceException::class);
});

test('validate academic year dates for multiple schools', function () {
    expect(fn () => $this->service->validateAcademicYearDatesForMultipleSchools(
        schoolIds: [],
        startDates: ['01/02/2024'],
        endDates: ['30/06/2024'],
        year: 2024
    ))->not()->toThrow(RuntimeException::class);
});

test('validate academic year modules for multiple schools', function () {
    expect(fn () => $this->service->validateAcademicYearModulesForMultipleSchools(
        schoolIds: [],
        year: 2024,
        stagesCount: 4
    ))->not()->toThrow(RuntimeException::class);
});

test('validate academic year modules for multiple schools skips existing', function () {
    $school2 = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
    ]);

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    $existingSchools = $this->service->getExistingAcademicYearSchools(
        [$this->school->cod_escola, $school2->cod_escola],
        $this->currentYear
    );

    expect($existingSchools)->toHaveCount(1);
    expect($existingSchools)->toContain($this->school->cod_escola);

    $schoolsToProcess = array_diff([$this->school->cod_escola, $school2->cod_escola], $existingSchools);

    expect($schoolsToProcess)->toHaveCount(1);
    expect($schoolsToProcess)->toContain($school2->cod_escola);

    $this->service->validateAcademicYearModulesForMultipleSchools(
        schoolIds: $schoolsToProcess,
        year: $this->currentYear,
        stagesCount: 2
    );
});

test('validate academic year dates for multiple schools skips existing', function () {
    $school2 = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
    ]);

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    $existingSchools = $this->service->getExistingAcademicYearSchools(
        [$this->school->cod_escola, $school2->cod_escola],
        $this->currentYear
    );

    $schoolsToProcess = array_diff([$this->school->cod_escola, $school2->cod_escola], $existingSchools);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];

    $this->service->validateAcademicYearDatesForMultipleSchools(
        schoolIds: $schoolsToProcess,
        startDates: $startDates,
        endDates: $endDates,
        year: $this->currentYear
    );

    expect(true)->toBeTrue();
});

test('create academic year with copy school classes', function () {
    $setup = ($this->setupBasicCopyTest)();
    ($this->createDefaultClass)();

    $academicYear = $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $setup['dates']['startDates'],
        endDates: $setup['dates']['endDates'],
        schoolDays: $setup['dates']['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $setup['user']->getKey()
    );

    $this->assertInstanceOf(LegacySchoolAcademicYear::class, $academicYear);
    $this->assertEquals($this->currentYear, $academicYear->ano);
    $this->assertEquals($this->school->cod_escola, $academicYear->ref_cod_escola);

    $copiedClass = LegacySchoolClass::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ano', $this->currentYear)
        ->where('nm_turma', 'Turma A')
        ->first();

    $this->assertNotNull($copiedClass);
    $this->assertEquals($this->currentYear, $copiedClass->ano);
});

test('create academic year with copy teacher data', function () {
    $setup = ($this->setupTeacherCopyTest)();

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $setup['dates']['startDates'],
        endDates: $setup['dates']['endDates'],
        schoolDays: $setup['dates']['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: true,
        copyEmployeeData: false,
        userId: $setup['user']->getKey()
    );

    $copiedClasses = LegacySchoolClass::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ano', $this->currentYear)
        ->get();

    $copiedTeachers = LegacySchoolClassTeacher::query()
        ->where('turma_id', $copiedClasses->first()->cod_turma)
        ->where('ano', $this->currentYear)
        ->get();

    expect($copiedClasses)->toHaveCount(1);
    expect($copiedTeachers)->toHaveCount(1);
});

test('create academic year with copy employee allocations', function () {
    $setup = ($this->setupEmployeeCopyTest)();

    $previousAllocations = EmployeeAllocation::query()
        ->where('ano', $this->previousYear)
        ->where('ref_cod_escola', $this->school->cod_escola)
        ->get();
    expect($previousAllocations)->toHaveCount(1);

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $setup['dates']['startDates'],
        endDates: $setup['dates']['endDates'],
        schoolDays: $setup['dates']['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: false,
        copyEmployeeData: true,
        userId: $setup['user']->getKey()
    );

    $copiedAllocations = EmployeeAllocation::query()
        ->where('ano', $this->currentYear)
        ->where('ref_cod_escola', $this->school->cod_escola)
        ->get();

    expect($copiedAllocations)->toHaveCount(1);
    expect($copiedAllocations->first()->ref_cod_servidor)->toBe($setup['employeeData']['employee']->cod_servidor);
    expect($copiedAllocations->first()->carga_horaria)->toBe('00:00:40');
});

test('create academic year with copy teacher and employee allocations', function () {
    $user = LegacyUserFactory::new()->create();
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->previousYear,
        'ativo' => 1,
    ]);
    LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ref_cod_serie' => $this->grade->cod_serie,
        'ref_cod_curso' => $this->course->cod_curso,
        'ano' => $this->previousYear,
        'nm_turma' => 'Turma A',
    ]);

    $teacherRole = LegacyRoleFactory::new()->create(['professor' => 1]);
    $employeeRole = LegacyRoleFactory::new()->create(['professor' => 0]);

    $teacher = EmployeeFactory::new()->create();
    $employee = EmployeeFactory::new()->create();

    $teacherEmployeeRole = LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_funcao' => $teacherRole->cod_funcao,
        'ref_cod_servidor' => $teacher->cod_servidor,
        'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
    ]);

    $employeeEmployeeRole = LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_funcao' => $employeeRole->cod_funcao,
        'ref_cod_servidor' => $employee->cod_servidor,
        'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
    ]);

    EmployeeAllocationFactory::new()->create([
        'ano' => $this->previousYear,
        'ref_cod_escola' => $this->school->cod_escola,
        'ref_cod_servidor' => $teacher->cod_servidor,
        'ref_cod_servidor_funcao' => $teacherEmployeeRole->cod_servidor_funcao,
        'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'ativo' => 1,
        'carga_horaria' => 40,
        'periodo' => 1,
        'hora_inicial' => '07:00:00',
        'hora_final' => '12:00:00',
        'dia_semana' => 2,
        'data_admissao' => '2020-01-01',
        'created_by' => 1,
        'deleted_by' => null,
    ]);

    EmployeeAllocationFactory::new()->create([
        'ano' => $this->previousYear,
        'ref_cod_escola' => $this->school->cod_escola,
        'ref_cod_servidor' => $employee->cod_servidor,
        'ref_cod_servidor_funcao' => $employeeEmployeeRole->cod_servidor_funcao,
        'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'ativo' => 1,
        'carga_horaria' => 30,
        'periodo' => 2,
        'hora_inicial' => '13:00:00',
        'hora_final' => '18:00:00',
        'dia_semana' => 3,
        'data_admissao' => '2020-01-01',
        'created_by' => 1,
        'deleted_by' => null,
    ]);

    $previousAllocations = EmployeeAllocation::query()
        ->where('ano', $this->previousYear)
        ->where('ref_cod_escola', $this->school->cod_escola)
        ->get();
    expect($previousAllocations)->toHaveCount(2);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = [100, 100];

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: true,
        copyEmployeeData: true,
        userId: $user->getKey()
    );

    $copiedAllocations = EmployeeAllocation::query()
        ->where('ano', $this->currentYear)
        ->where('ref_cod_escola', $this->school->cod_escola)
        ->get();

    expect($copiedAllocations)->toHaveCount(2);

    $teacherAllocation = $copiedAllocations->where('ref_cod_servidor', $teacher->cod_servidor)->first();
    $employeeAllocation = $copiedAllocations->where('ref_cod_servidor', $employee->cod_servidor)->first();

    expect($teacherAllocation)->not->toBeNull();
    expect($employeeAllocation)->not->toBeNull();
    expect($teacherAllocation->carga_horaria)->toBe('00:00:40');
    expect($employeeAllocation->carga_horaria)->toBe('00:00:30');
});

test('validate academic year dates without conflict', function () {

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => 2022,
        'ativo' => 1,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => 2022,
        'data_inicio' => '2022-03-01',
        'data_fim' => '2022-06-30',
    ]);

    $startDates = ['01/07/2023'];
    $endDates = ['30/12/2023'];

    expect(fn () => $this->service->validateAcademicYearDates(
        startDates: $startDates,
        endDates: $endDates,
        year: 2023,
        schoolId: $this->school->cod_escola
    ))->not()->toThrow(RuntimeException::class);
});

test('validate academic year modules with data in stages', function () {

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    for ($i = 1; $i <= 4; $i++) {
        LegacyAcademicYearStageFactory::new()->create([
            'ref_ref_cod_escola' => $this->school->cod_escola,
            'ref_ano' => $this->currentYear,
            'sequencial' => $i,
        ]);
    }

    expect(fn () => $this->service->validateAcademicYearModules(
        year: $this->currentYear,
        schoolId: $this->school->cod_escola,
        stagesCount: 2
    ))->not()->toThrow(RuntimeException::class);
});

test('create academic year stages with correct sequencial', function () {
    $user = LegacyUserFactory::new()->create();
    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear, '01/08/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/07/' . $this->currentYear, '30/11/' . $this->currentYear];
    $schoolDays = [100, 100, 100];

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->orderBy('sequencial')
        ->get();

    expect($stages)->toHaveCount(3);
    expect($stages[0]->sequencial)->toBe(1);
    expect($stages[1]->sequencial)->toBe(2);
    expect($stages[2]->sequencial)->toBe(3);
});

test('create academic year with multigrade class', function () {
    $user = LegacyUserFactory::new()->create();

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->previousYear,
        'ativo' => 1,
    ]);

    $originalClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ref_cod_serie' => $this->grade->cod_serie,
        'ref_cod_curso' => $this->course->cod_curso,
        'ano' => $this->previousYear,
        'nm_turma' => 'Turma Multisseriada',
        'multiseriada' => 1,
    ]);

    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $this->school->cod_escola,
        'turma_id' => $originalClass->cod_turma,
        'serie_id' => $this->grade->cod_serie,
    ]);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = [100, 100];

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    $copiedClasses = LegacySchoolClass::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ano', $this->currentYear)
        ->get();

    $copiedClassGrades = LegacySchoolClassGrade::query()
        ->where('turma_id', $copiedClasses->first()->cod_turma)
        ->get();

    expect($copiedClasses)->toHaveCount(1);
    expect($copiedClassGrades)->toHaveCount(1);
});

test('create academic year with disciplines', function () {
    $user = LegacyUserFactory::new()->create();

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->previousYear,
        'ativo' => 1,
    ]);

    $originalClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ref_cod_serie' => $this->grade->cod_serie,
        'ref_cod_curso' => $this->course->cod_curso,
        'ano' => $this->previousYear,
        'nm_turma' => 'Turma A',
    ]);

    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineSchoolClassFactory::new()->create([
        'turma_id' => $originalClass->cod_turma,
        'componente_curricular_id' => $discipline->id,
        'escola_id' => $this->school->cod_escola,
        'ano_escolar_id' => $this->grade->cod_serie,
    ]);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = [100, 100];

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    $copiedClasses = LegacySchoolClass::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ano', $this->currentYear)
        ->get();

    $copiedDisciplines = LegacyDisciplineSchoolClass::query()
        ->where('turma_id', $copiedClasses->first()->cod_turma)
        ->get();

    expect($copiedClasses)->toHaveCount(1);
    expect($copiedDisciplines)->toHaveCount(1);
    expect($copiedDisciplines->first()->componente_curricular_id)->toBe($discipline->id);
});

test('create academic year with class modules', function () {
    $user = LegacyUserFactory::new()->create();

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->previousYear,
        'ativo' => 1,
    ]);

    $originalClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ref_cod_serie' => $this->grade->cod_serie,
        'ref_cod_curso' => $this->course->cod_curso,
        'ano' => $this->previousYear,
        'nm_turma' => 'Turma A',
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $originalClass->cod_turma,
        'data_inicio' => $this->previousYear . '-02-01',
        'data_fim' => $this->previousYear . '-06-30',
        'dias_letivos' => 120,
    ]);

    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = [100, 100];

    $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: true,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    $copiedClasses = LegacySchoolClass::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ano', $this->currentYear)
        ->get();

    $copiedModules = LegacySchoolClassStage::query()
        ->where('ref_cod_turma', $copiedClasses->first()->cod_turma)
        ->get();

    expect($copiedClasses)->toHaveCount(1);
    expect($copiedModules)->toHaveCount(1);
    expect($copiedModules->first()->data_inicio->format('Y-m-d'))->toBe($this->currentYear . '-02-01');
    expect($copiedModules->first()->data_fim->format('Y-m-d'))->toBe($this->currentYear . '-06-30');
    expect($copiedModules->first()->dias_letivos)->toBe(120);
});

test('validate academic year dates single school', function () {
    expect(fn () => $this->service->validateAcademicYearDates(
        startDates: ['01/02/2024'],
        endDates: ['30/06/2024'],
        year: 2024,
        schoolId: $this->school->cod_escola
    ))->not()->toThrow(RuntimeException::class);
});

test('validate academic year modules single school', function () {
    expect(fn () => $this->service->validateAcademicYearModules(
        year: $this->currentYear,
        schoolId: $this->school->cod_escola,
        stagesCount: 4
    ))->not()->toThrow(RuntimeException::class);
});

test('validate academic year dates with conflict', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => 2022,
        'ativo' => 1,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => 2022,
        'data_inicio' => '2022-03-01',
        'data_fim' => '2022-06-30',
    ]);

    $startDates = ['01/04/2022'];
    $endDates = ['30/06/2022'];

    expect(fn () => $this->service->validateAcademicYearDates(
        startDates: $startDates,
        endDates: $endDates,
        year: 2023,
        schoolId: $this->school->cod_escola
    ))->toThrow(AcademicYearServiceException::class, 'A data informada não pode fazer parte do período configurado para outros anos letivos.');
});

test('get school academic year not found', function () {
    expect(fn () => $this->service->updateAcademicYearStages(
        schoolId: 999999,
        year: 9999,
        startDates: ['01/02/2024'],
        endDates: ['30/06/2024'],
        schoolDays: [100],
        moduleId: $this->stageType->cod_modulo
    ))->toThrow(AcademicYearServiceException::class, 'Ano letivo não encontrado para a escola');
});

test('validate academic year modules should not validate existing schools with data', function () {
    $school2 = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
    ]);

    $academicYear = LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'escola_ano_letivo_id' => $academicYear->id,
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => $this->currentYear,
        'sequencial' => 1,
        'ref_cod_modulo' => $this->stageType->cod_modulo,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'escola_ano_letivo_id' => $academicYear->id,
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => $this->currentYear,
        'sequencial' => 2,
        'ref_cod_modulo' => $this->stageType->cod_modulo,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'escola_ano_letivo_id' => $academicYear->id,
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => $this->currentYear,
        'sequencial' => 3,
        'ref_cod_modulo' => $this->stageType->cod_modulo,
    ]);

    $existingSchools = $this->service->getExistingAcademicYearSchools(
        [$this->school->cod_escola, $school2->cod_escola],
        $this->currentYear
    );

    $schoolsToProcess = array_diff([$this->school->cod_escola, $school2->cod_escola], $existingSchools);

    expect($schoolsToProcess)->toHaveCount(1);
    expect($schoolsToProcess)->toContain($school2->cod_escola);
    expect($schoolsToProcess)->not()->toContain($this->school->cod_escola);

    $this->service->validateAcademicYearModulesForMultipleSchools(
        schoolIds: $schoolsToProcess,
        year: $this->currentYear,
        stagesCount: 1
    );
});

test('create academic year reactivates deactivated academic year', function () {
    $user = ($this->createUser)();

    $deactivatedAcademicYear = LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 0,
        'andamento' => 2,
    ]);

    $dates = ($this->getDefaultDates)();

    $result = $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $dates['startDates'],
        endDates: $dates['endDates'],
        schoolDays: $dates['schoolDays'],
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    expect($result->id)->toBe($deactivatedAcademicYear->id);
    expect($result->ativo)->toBe(1);
    expect($result->andamento)->toBe(0);
    expect($result->ref_usuario_cad)->toBe($user->getKey());
});

test('create academic year with empty school days', function () {
    $user = ($this->createUser)();
    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = ['', null];

    $result = $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    expect($result)->toBeInstanceOf(LegacySchoolAcademicYear::class);

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->orderBy('sequencial')
        ->get();

    expect($stages)->toHaveCount(2);
    expect($stages[0]->dias_letivos)->toBeNull();
    expect($stages[1]->dias_letivos)->toBeNull();
});

test('create academic year with mixed school days', function () {
    $user = ($this->createUser)();
    $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
    $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
    $schoolDays = ['100', ''];

    $result = $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: $startDates,
        endDates: $endDates,
        schoolDays: $schoolDays,
        moduleId: $this->stageType->cod_modulo,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: $user->getKey()
    );

    expect($result)->toBeInstanceOf(LegacySchoolAcademicYear::class);

    $stages = LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->cod_escola)
        ->where('ref_ano', $this->currentYear)
        ->orderBy('sequencial')
        ->get();

    expect($stages)->toHaveCount(2);
    expect($stages[0]->dias_letivos)->toBe(100);
    expect($stages[1]->dias_letivos)->toBeNull();
});

test('process academic year batch with empty schools', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
});

test('process academic year batch with empty periodos', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
});

test('process academic year batch with empty year', function () {
    $params = [
        'year' => null,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
});

test('process academic year batch with invalid year', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
});

test('process academic year batch with non array schools', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => 'not_an_array',
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['message'])->toContain('Parâmetros de escola e períodos devem ser arrays');
});

test('process academic year batch with non array periodos', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => 'not_an_array',
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['message'])->toContain('Parâmetros de escola e períodos devem ser arrays');
});

test('process academic year batch with invalid school', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [999999],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['errors'])->toBeArray();
    expect($result['errors'])->not()->toBeEmpty();
});

test('process academic year batch with date conflict', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => 2022,
        'ativo' => 1,
    ]);

    LegacyAcademicYearStageFactory::new()->create([
        'ref_ref_cod_escola' => $this->school->cod_escola,
        'ref_ano' => 2022,
        'data_inicio' => '2022-03-01',
        'data_fim' => '2022-06-30',
    ]);

    $params = [
        'year' => 2023,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/04/2022', 'data_fim' => '30/06/2022'],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('failed');
    expect($result['errors'])->toBeArray();
    expect($result['errors'])->not()->toBeEmpty();
});

test('process academic year batch with existing academic year', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(0);
    expect($result['skipped'])->toBe(1);
    expect($result['details'])->toHaveCount(1);
    expect($result['details'][0]['type'])->toBe('skipped');
    expect($result['details'][0]['message'])->toContain('já está aberto');
});

test('process academic year batch successful', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['skipped'])->toBe(0);
    expect($result['details'])->toHaveCount(1);
    expect($result['details'][0]['type'])->toBe('success');
    expect($result['details'][0]['message'])->toContain('criado com sucesso');
});

test('process academic year batch with multiple schools mixed results', function () {
    $school2 = ($this->createAdditionalSchool)();
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $school2->cod_escola,
        'ano' => $this->currentYear,
        'ativo' => 1,
    ]);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola, $school2->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['skipped'])->toBe(1);
    expect($result['details'])->toHaveCount(2);

    $successDetails = collect($result['details'])->where('type', 'success')->first();
    $skippedDetails = collect($result['details'])->where('type', 'skipped')->first();

    expect($successDetails)->not()->toBeNull();
    expect($skippedDetails)->not()->toBeNull();
    expect($successDetails['message'])->toContain('criado com sucesso');
    expect($skippedDetails['message'])->toContain('já está aberto');
});

test('process academic year batch with periods filtering', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '', 'data_fim' => ''],
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ['data_inicio' => '', 'data_fim' => ''],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['skipped'])->toBe(0);
});

test('process academic year batch with copy classes data', function () {
    ($this->createPreviousAcademicYear)();
    ($this->createDefaultClass)();

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->toContain('copiadas 1 turma(s)');
});

test('process academic year batch with copy teacher data', function () {
    ($this->createPreviousAcademicYear)();
    $employeeData = ($this->createEmployeeWithRole)(isProfessor: true);
    ($this->createEmployeeAllocation)($employeeData);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => true,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) de professor(es)');
});

test('process academic year batch with copy employee data', function () {
    ($this->createPreviousAcademicYear)();
    $employeeData = ($this->createEmployeeWithRole)(isProfessor: false);
    ($this->createEmployeeAllocation)($employeeData);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => true,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) dos demais servidor(es)');
});

test('process academic year batch with all copy options', function () {
    ($this->createPreviousAcademicYear)();
    ($this->createDefaultClass)();
    $teacherData = ($this->createEmployeeWithRole)(isProfessor: true);
    $employeeData = ($this->createEmployeeWithRole)(isProfessor: false);
    ($this->createEmployeeAllocation)($teacherData);
    ($this->createEmployeeAllocation)($employeeData);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => true,
        'copyEmployeeData' => true,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->toContain('copiadas 1 turma(s)');
    expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) de professor(es)');
    expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) dos demais servidor(es)');
});

test('process academic year batch with no copy options', function () {
    ($this->createPreviousAcademicYear)();
    ($this->createDefaultClass)();
    $employeeData = ($this->createEmployeeWithRole)(isProfessor: true);
    ($this->createEmployeeAllocation)($employeeData);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => $this->stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => false,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->not()->toContain('copiadas');
});

test('process academic year batch with invalid module', function () {
    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
        ],
        'moduleId' => 30000,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    expect(fn () => $this->service->createAcademicYearBatch($params))->toThrow(QueryException::class);
});

test('process academic year batch validates stage count', function () {
    $stageType = LegacyStageTypeFactory::new()->create([
        'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        'nm_tipo' => 'Trimestral',
        'num_etapas' => 3,
    ]);

    $params = [
        'year' => $this->currentYear,
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ['data_inicio' => '01/05/' . $this->currentYear, 'data_fim' => '31/08/' . $this->currentYear],
            ['data_inicio' => '01/09/' . $this->currentYear, 'data_fim' => '30/12/' . $this->currentYear],
            ['data_inicio' => '01/01/' . $this->currentYear, 'data_fim' => '31/01/' . $this->currentYear],
        ],
        'moduleId' => $stageType->cod_modulo,
        'user' => ($this->createUser)(),
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
    ];

    $result = $this->service->createAcademicYearBatch($params);

    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
});

test('validate academic year dates throws exception with invalid dates', function () {
    expect(fn () => $this->service->validateAcademicYearDates(
        ['invalid_date'],
        ['30/04/' . $this->currentYear],
        $this->currentYear,
        $this->school->cod_escola
    ))->toThrow(Exception::class);
});

test('validate stage count with module throws exception with invalid module', function () {
    expect(fn () => $this->service->validateStageCountWithModule(
        999999,
        1
    ))->toThrow(Exception::class);
});

test('create academic year for school throws exception with invalid module', function () {
    expect(fn () => $this->service->createAcademicYearForSchool(
        schoolId: $this->school->cod_escola,
        year: $this->currentYear,
        startDates: ['01/02/' . $this->currentYear],
        endDates: ['30/04/' . $this->currentYear],
        schoolDays: [90],
        moduleId: 999999,
        copySchoolClasses: false,
        copyTeacherData: false,
        copyEmployeeData: false,
        userId: ($this->createUser)()->id
    ))->toThrow(Exception::class);
});

test('update academic year stages throws exception with invalid school', function () {
    expect(fn () => $this->service->updateAcademicYearStages(
        schoolId: 999999,
        year: $this->currentYear,
        startDates: ['01/02/' . $this->currentYear],
        endDates: ['30/04/' . $this->currentYear],
        schoolDays: [90],
        moduleId: $this->stageType->cod_modulo
    ))->toThrow(Exception::class);
});

test('execute copy academic years copies data successfully', function () {
    $schoolId = $this->school->cod_escola;
    $previousYear = $this->previousYear;

    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $schoolId,
        'ano' => $previousYear,
        'ativo' => 1,
    ]);

    $schoolCourse = LegacySchoolCourse::query()
        ->where('ref_cod_escola', $schoolId)
        ->where('ref_cod_curso', $this->course->cod_curso)
        ->first();
    $schoolCourse->update(['anos_letivos' => '{' . $previousYear . '}']);

    $schoolGrade = LegacySchoolGrade::query()
        ->where('ref_cod_escola', $schoolId)
        ->where('ref_cod_serie', $this->grade->cod_serie)
        ->first();
    $schoolGrade->update(['anos_letivos' => '{' . $previousYear . '}']);

    $discipline = LegacyDisciplineFactory::new()->create();
    $schoolGradeDiscipline = LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $schoolId,
        'ref_ref_cod_serie' => $this->grade->cod_serie,
        'ref_cod_disciplina' => $discipline->id,
        'anos_letivos' => '{' . $previousYear . '}',
    ]);

    $curriculumComponent = LegacyDisciplineAcademicYearFactory::new()->create([
        'componente_curricular_id' => $discipline->id,
        'ano_escolar_id' => $this->grade->cod_serie,
        'anos_letivos' => '{' . $previousYear . '}',
    ]);

    $evaluationRule = LegacyEvaluationRuleFactory::new()->create();
    $evaluationRuleSeriesYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
        'serie_id' => $this->grade->cod_serie,
        'regra_avaliacao_id' => $evaluationRule->id,
        'ano_letivo' => $previousYear,
    ]);

    expect($schoolCourse->anos_letivos)->toContain($previousYear);
    expect($schoolCourse->anos_letivos)->not()->toContain($this->currentYear);
    expect($schoolGrade->anos_letivos)->toContain($previousYear);
    expect($schoolGrade->anos_letivos)->not()->toContain($this->currentYear);
    expect($schoolGradeDiscipline->anos_letivos)->toContain($previousYear);
    expect($schoolGradeDiscipline->anos_letivos)->not()->toContain($this->currentYear);
    expect($curriculumComponent->anos_letivos)->toContain($previousYear);
    expect($curriculumComponent->anos_letivos)->not()->toContain($this->currentYear);

    $this->service->executeCopyAcademicYears(
        year: $this->currentYear,
        schoolId: $schoolId
    );

    $schoolCourse->refresh();
    $schoolGrade->refresh();
    $schoolGradeDiscipline->refresh();
    $curriculumComponent->refresh();

    expect($schoolCourse->anos_letivos)->toContain($this->currentYear);
    expect($schoolGrade->anos_letivos)->toContain($this->currentYear);
    expect($schoolGradeDiscipline->anos_letivos)->toContain($this->currentYear);
    expect($curriculumComponent->anos_letivos)->toContain($this->currentYear);

    $newEvaluationRule = LegacyEvaluationRuleGradeYear::query()
        ->where('serie_id', $this->grade->cod_serie)
        ->where('ano_letivo', $this->currentYear)
        ->first();

    expect($newEvaluationRule)->not()->toBeNull();
    expect($newEvaluationRule->regra_avaliacao_id)->toBe($evaluationRule->id);
});

test('process academic year batch with copy school classes data', function () {
    ($this->createPreviousAcademicYear)();
    ($this->createDefaultClass)();

    $params = [
        'schools' => [$this->school->cod_escola],
        'periodos' => [
            ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear, 'dias_letivos' => 90],
        ],
        'year' => $this->currentYear,
        'moduleId' => $this->stageType->cod_modulo,
        'copySchoolClasses' => true,
        'copyTeacherData' => false,
        'copyEmployeeData' => false,
        'user' => ($this->createUser)(),
    ];

    $result = $this->service->createAcademicYearBatch($params);

    $this->assertEquals('completed', $result['status']);
    $this->assertEquals(1, $result['processed']);
    $this->assertEquals(0, $result['skipped']);
});

test('abrir e fechar em lote validam os parametros', function () {
    $cases = [
        [['schools' => []], 'Parâmetros de escola e ano letivo são obrigatórios.'],
        [['schools' => 'x'], 'Parâmetro de escola deve ser um array.'],
        [['year' => '2024.5'], 'Ano letivo deve ser um número inteiro.'],
    ];

    foreach ($cases as [$overrides, $message]) {
        $result = $this->service->openAcademicYearBatch(($this->batchActionParams)($overrides));

        expect($result['status'])->toBe('failed');
        expect($result['errors'])->toBe([['error' => $message]]);
    }
});

test('abrir e fechar em lote recusam escola desconhecida e ano letivo inexistente', function () {
    $result = $this->service->openAcademicYearBatch(($this->batchActionParams)(['schools' => [999999999]]));
    expect($result['errors'][0]['error'])->toBe('Escola ID 999999999 não encontrada ou inativa.');

    $result = $this->service->closeAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain("Ano letivo {$this->currentYear} não encontrado ou inativo.");
    expect($result['message'])->toBe('Processamento cancelado devido a erros de validação.');
});

test('abrir em lote recusa ano em andamento ou finalizado e abre o nao iniciado', function () {
    $academicYear = LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => $this->currentYear, 'andamento' => AcademicYearService::STATUS_IN_PROGRESS]);

    $result = $this->service->openAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain('pois já está em andamento.');

    $academicYear->update(['andamento' => AcademicYearService::STATUS_FINISHED]);
    $result = $this->service->openAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain('pois já está finalizado.');

    $academicYear->update(['andamento' => AcademicYearService::STATUS_NOT_STARTED]);
    $result = $this->service->openAcademicYearBatch(($this->batchActionParams)());
    expect($result['status'])->toBe('completed');
    expect($result['processed'])->toBe(1);
    expect($result['details'][0]['message'])->toContain('inicializado com sucesso');
    expect($academicYear->refresh()->andamento)->toBe(AcademicYearService::STATUS_IN_PROGRESS);
});

test('fechar em lote recusa ano finalizado nao iniciado ou com matriculas em andamento', function () {
    $academicYear = LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => $this->currentYear, 'andamento' => AcademicYearService::STATUS_FINISHED]);

    $result = $this->service->closeAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain('já está finalizado.');

    $academicYear->update(['andamento' => AcademicYearService::STATUS_NOT_STARTED]);
    $result = $this->service->closeAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain('não está iniciado.');

    $academicYear->update(['andamento' => AcademicYearService::STATUS_IN_PROGRESS]);
    LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_escola' => $this->school,
        'ref_cod_curso' => $this->course,
        'ref_ref_cod_serie' => $this->grade,
        'ano' => $this->currentYear,
        'aprovado' => 3,
        'ativo' => 1,
        'ultima_matricula' => 1,
    ]);
    $result = $this->service->closeAcademicYearBatch(($this->batchActionParams)());
    expect($result['errors'][0]['error'])->toContain('Existem matrículas em andamento.');

    LegacyRegistration::query()->where('ref_ref_cod_escola', $this->school->cod_escola)->update(['aprovado' => 1]);
    $result = $this->service->closeAcademicYearBatch(($this->batchActionParams)());
    expect($result['status'])->toBe('completed');
    expect($result['details'][0]['message'])->toContain('finalizado com sucesso');
    expect($academicYear->refresh()->andamento)->toBe(AcademicYearService::STATUS_FINISHED);
});

test('criar em lote recusa ano que nao e inteiro', function () {
    $result = $this->service->createAcademicYearBatch([
        'year' => '2024.5',
        'schools' => [$this->school->cod_escola],
        'periodos' => [['data_inicio' => '01/02/2024', 'data_fim' => '30/04/2024']],
    ]);

    expect($result['errors'][0]['error'])->toBe('Ano letivo deve ser um número inteiro.');
});

test('nao remove etapas com faltas gerais notas ou lancamentos no diario online', function () {
    $academicYear = LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => $this->currentYear]);
    foreach ([1, 2] as $sequence) {
        LegacyAcademicYearStageFactory::new()->create(['escola_ano_letivo_id' => $academicYear, 'ref_ano' => $this->currentYear, 'ref_ref_cod_escola' => $this->school->cod_escola, 'sequencial' => $sequence, 'ref_cod_modulo' => $this->stageType]);
    }
    $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_escola' => $this->school, 'ano' => $this->currentYear, 'ativo' => 1]);

    $absence = LegacyGeneralAbsenceFactory::new()->create(['falta_aluno_id' => LegacyStudentAbsenceFactory::new()->create(['matricula_id' => $registration]), 'etapa' => 2]);
    try {
        $this->service->validateAcademicYearModules($this->currentYear, $this->school->cod_escola, 1);
        $this->fail('Deveria barrar pela falta geral.');
    } catch (AcademicYearServiceException $e) {
        expect($e->getMessage())->toBe('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
    }
    $absence->delete();

    $score = LegacyDisciplineScoreFactory::new()->create(['nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]), 'etapa' => 2]);
    try {
        $this->service->validateAcademicYearModules($this->currentYear, $this->school->cod_escola, 1);
        $this->fail('Deveria barrar pela nota.');
    } catch (AcademicYearServiceException $e) {
        expect($e->getMessage())->toBe('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
    }
    $score->delete();

    config(['legacy.config.url_novo_educacao' => 'https://idiario.example', 'legacy.config.token_novo_educacao' => 'token']);
    $this->mock(iDiarioService::class)->shouldReceive('getStepActivityByUnit')->with($this->school->cod_escola, $this->currentYear, 2)->andReturn(false, true);

    $this->service->validateAcademicYearModules($this->currentYear, $this->school->cod_escola, 1);

    $this->expectException(AcademicYearServiceException::class);
    $this->expectExceptionMessage('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
    $this->service->validateAcademicYearModules($this->currentYear, $this->school->cod_escola, 1);
});
