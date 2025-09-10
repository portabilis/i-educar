<?php

namespace Tests\Unit\Services;

use App\Exceptions\AcademicYearServiceException;
use App\Models\EmployeeAllocation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyEvaluationRuleGradeYear;
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
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
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
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RuntimeException;
use Tests\TestCase;

class AcademicYearServiceTest extends TestCase
{
    use DatabaseTransactions;

    private AcademicYearService $service;

    private $school;

    private $course;

    private $grade;

    private $stageType;

    private $currentYear;

    private $previousYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AcademicYearService;

        $this->setupTestData();
    }

    private function setupTestData(): void
    {
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
    }

    private function createUser(): LegacyUser
    {
        return LegacyUserFactory::new()->create();
    }

    private function getDefaultDates(): array
    {
        return [
            'startDates' => ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear],
            'endDates' => ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear],
            'schoolDays' => [100, 100],
        ];
    }

    private function createPreviousAcademicYear(): LegacySchoolAcademicYear
    {
        return LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->previousYear,
            'ativo' => 1,
        ]);
    }

    private function createDefaultClass(?int $year = null): LegacySchoolClass
    {
        $year = $year ?? $this->previousYear;

        return LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->school->cod_escola,
            'ref_ref_cod_serie' => $this->grade->cod_serie,
            'ref_cod_curso' => $this->course->cod_curso,
            'ano' => $year,
            'nm_turma' => 'Turma A',
        ]);
    }

    private function createAdditionalSchool(): LegacySchool
    {
        return LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);
    }

    private function createCurrentAcademicYear(): LegacySchoolAcademicYear
    {
        return LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 1,
        ]);
    }

    private function createTeacherWithDiscipline(LegacySchoolClass $class): array
    {
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
    }

    private function createEmployeeWithRole(bool $isProfessor = false): array
    {
        $role = LegacyRoleFactory::new()->create(['professor' => $isProfessor ? 1 : 0]);
        $employee = EmployeeFactory::new()->create();
        $employeeRole = LegacyEmployeeRoleFactory::new()->create([
            'ref_cod_funcao' => $role->cod_funcao,
            'ref_cod_servidor' => $employee->cod_servidor,
            'ref_ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);

        return ['employee' => $employee, 'role' => $role, 'employeeRole' => $employeeRole];
    }

    private function createEmployeeAllocation(array $employeeData, ?int $year = null): EmployeeAllocation
    {
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
    }

    private function setupBasicCopyTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();
        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'dates' => $dates,
        ];
    }

    private function setupTeacherCopyTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();
        $originalClass = $this->createDefaultClass();
        $teacherData = $this->createTeacherWithDiscipline($originalClass);
        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'teacherData' => $teacherData,
            'dates' => $dates,
        ];
    }

    private function setupEmployeeCopyTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();
        $employeeData = $this->createEmployeeWithRole(isProfessor: false);
        $this->createEmployeeAllocation($employeeData);
        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'employeeData' => $employeeData,
            'dates' => $dates,
        ];
    }

    private function setupCompleteCopyTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();
        $originalClass = $this->createDefaultClass();
        $teacherData = $this->createTeacherWithDiscipline($originalClass);
        $employeeData = $this->createEmployeeWithRole(isProfessor: false);
        $this->createEmployeeAllocation($employeeData);
        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'teacherData' => $teacherData,
            'employeeData' => $employeeData,
            'dates' => $dates,
        ];
    }

    private function setupMultigradeClassTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();

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

        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'dates' => $dates,
        ];
    }

    private function setupDisciplinesTest(): array
    {
        $user = $this->createUser();
        $this->createPreviousAcademicYear();
        $originalClass = $this->createDefaultClass();

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'turma_id' => $originalClass->cod_turma,
            'componente_curricular_id' => $discipline->id,
            'escola_id' => $this->school->cod_escola,
            'ano_escolar_id' => $this->grade->cod_serie,
        ]);

        $dates = $this->getDefaultDates();

        return [
            'user' => $user,
            'originalClass' => $originalClass,
            'discipline' => $discipline,
            'dates' => $dates,
        ];
    }

    public function test_create_academic_year_for_school_creates_academic_year(): void
    {
        $user = $this->createUser();
        $dates = $this->getDefaultDates();

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
    }

    public function test_create_academic_year_for_school_creates_stages(): void
    {
        $user = $this->createUser();
        $dates = $this->getDefaultDates();

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
    }

    public function test_create_academic_year_for_multiple_schools(): void
    {
        $user = $this->createUser();
        $school2 = $this->createAdditionalSchool();
        $dates = $this->getDefaultDates();

        $result = $this->service->createAcademicYearForMultipleSchools(
            schoolIds: [$this->school->cod_escola, $school2->cod_escola],
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

        expect($result)->toHaveKey('processed');
        expect($result)->toHaveKey('skipped');
        expect($result['processed'])->toHaveCount(2);
        expect($result['skipped'])->toHaveCount(0);

        $academicYears = LegacySchoolAcademicYear::query()
            ->whereIn('ref_cod_escola', [$this->school->cod_escola, $school2->cod_escola])
            ->where('ano', $this->currentYear)
            ->get();

        expect($academicYears)->toHaveCount(2);
    }

    public function test_create_academic_year_for_multiple_schools_skips_existing(): void
    {
        $user = LegacyUserFactory::new()->create();
        $school2 = LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $this->school->ref_cod_instituicao,
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 1,
        ]);

        $startDates = ['01/02/' . $this->currentYear, '01/05/' . $this->currentYear];
        $endDates = ['30/04/' . $this->currentYear, '31/08/' . $this->currentYear];
        $schoolDays = [100, 100];

        $result = $this->service->createAcademicYearForMultipleSchools(
            schoolIds: [$this->school->cod_escola, $school2->cod_escola],
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

        expect($result['processed'])->toHaveCount(1);
        expect($result['skipped'])->toHaveCount(1);
        expect($result['skipped'])->toContain($this->school->cod_escola);
    }

    public function test_update_academic_year_stages(): void
    {
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
    }

    public function test_delete_academic_year_returns_true_when_successful(): void
    {
        $user = $this->createUser();
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
    }

    public function test_validate_stage_count_with_module(): void
    {
        expect(fn () => $this->service->validateStageCountWithModule(
            moduleId: $this->stageType->cod_modulo,
            stagesCount: $this->stageType->num_etapas
        ))->not()->toThrow(RuntimeException::class);
    }

    public function test_validate_stage_count_with_module_invalid_count(): void
    {
        expect(fn () => $this->service->validateStageCountWithModule(
            moduleId: $this->stageType->cod_modulo,
            stagesCount: $this->stageType->num_etapas + 1
        ))->toThrow(AcademicYearServiceException::class);
    }

    public function test_validate_stage_count_with_nonexistent_module(): void
    {
        expect(fn () => $this->service->validateStageCountWithModule(
            moduleId: 9999,
            stagesCount: 4
        ))->toThrow(AcademicYearServiceException::class);
    }

    public function test_validate_academic_year_dates_for_multiple_schools(): void
    {
        expect(fn () => $this->service->validateAcademicYearDatesForMultipleSchools(
            schoolIds: [],
            startDates: ['01/02/2024'],
            endDates: ['30/06/2024'],
            year: 2024
        ))->not()->toThrow(RuntimeException::class);
    }

    public function test_validate_academic_year_modules_for_multiple_schools(): void
    {
        expect(fn () => $this->service->validateAcademicYearModulesForMultipleSchools(
            schoolIds: [],
            year: 2024,
            stagesCount: 4
        ))->not()->toThrow(RuntimeException::class);
    }

    public function test_validate_academic_year_modules_for_multiple_schools_skips_existing(): void
    {
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
    }

    public function test_validate_academic_year_dates_for_multiple_schools_skips_existing(): void
    {
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
    }

    public function test_create_academic_year_with_copy_school_classes(): void
    {
        $setup = $this->setupBasicCopyTest();
        $this->createDefaultClass();

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
    }

    public function test_create_academic_year_with_copy_teacher_data(): void
    {
        $setup = $this->setupTeacherCopyTest();

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
    }

    public function test_create_academic_year_with_copy_employee_allocations(): void
    {
        $setup = $this->setupEmployeeCopyTest();

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
    }

    public function test_create_academic_year_with_copy_teacher_and_employee_allocations(): void
    {
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
    }

    public function test_validate_academic_year_dates_without_conflict(): void
    {

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
    }

    public function test_validate_academic_year_modules_with_data_in_stages(): void
    {

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
    }

    public function test_create_academic_year_stages_with_correct_sequencial(): void
    {
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
    }

    public function test_create_academic_year_with_multigrade_class(): void
    {
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
    }

    public function test_create_academic_year_with_disciplines(): void
    {
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
    }

    public function test_create_academic_year_with_class_modules(): void
    {
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
    }

    public function test_validate_academic_year_dates_single_school(): void
    {
        expect(fn () => $this->service->validateAcademicYearDates(
            startDates: ['01/02/2024'],
            endDates: ['30/06/2024'],
            year: 2024,
            schoolId: $this->school->cod_escola
        ))->not()->toThrow(RuntimeException::class);
    }

    public function test_validate_academic_year_modules_single_school(): void
    {
        expect(fn () => $this->service->validateAcademicYearModules(
            year: $this->currentYear,
            schoolId: $this->school->cod_escola,
            stagesCount: 4
        ))->not()->toThrow(RuntimeException::class);
    }

    public function test_validate_academic_year_dates_with_conflict(): void
    {
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
    }

    public function test_get_school_academic_year_not_found(): void
    {
        expect(fn () => $this->service->updateAcademicYearStages(
            schoolId: 999999,
            year: 9999,
            startDates: ['01/02/2024'],
            endDates: ['30/06/2024'],
            schoolDays: [100],
            moduleId: $this->stageType->cod_modulo
        ))->toThrow(AcademicYearServiceException::class, 'Ano letivo não encontrado para a escola');
    }

    public function test_validate_academic_year_modules_should_not_validate_existing_schools_with_data(): void
    {
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
    }

    public function test_create_academic_year_reactivates_deactivated_academic_year(): void
    {
        $user = $this->createUser();

        $deactivatedAcademicYear = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 0,
            'andamento' => 2,
        ]);

        $dates = $this->getDefaultDates();

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
    }

    public function test_create_academic_year_for_multiple_schools_reactivates_deactivated(): void
    {
        $user = $this->createUser();
        $school2 = $this->createAdditionalSchool();

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $this->school->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 0,
            'andamento' => 2,
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school2->cod_escola,
            'ano' => $this->currentYear,
            'ativo' => 1,
        ]);

        $dates = $this->getDefaultDates();

        $result = $this->service->createAcademicYearForMultipleSchools(
            schoolIds: [$this->school->cod_escola, $school2->cod_escola],
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

        expect($result['processed'])->toHaveCount(1);
        expect($result['skipped'])->toHaveCount(1);
        expect($result['processed'])->toContain($this->school->cod_escola);
        expect($result['skipped'])->toContain($school2->cod_escola);

        $reactivatedAcademicYear = LegacySchoolAcademicYear::query()
            ->where('ref_cod_escola', $this->school->cod_escola)
            ->where('ano', $this->currentYear)
            ->first();

        expect($reactivatedAcademicYear->ativo)->toBe(1);
        expect($reactivatedAcademicYear->andamento)->toBe(0);
    }

    public function test_create_academic_year_with_empty_school_days(): void
    {
        $user = $this->createUser();
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
    }

    public function test_create_academic_year_with_mixed_school_days(): void
    {
        $user = $this->createUser();
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
    }

    public function test_process_academic_year_batch_with_empty_schools(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
    }

    public function test_process_academic_year_batch_with_empty_periodos(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
    }

    public function test_process_academic_year_batch_with_empty_year(): void
    {
        $params = [
            'year' => null,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['message'])->toContain('Parâmetros de escola, períodos e ano letivo são obrigatórios');
    }

    public function test_process_academic_year_batch_with_invalid_year(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
    }

    public function test_process_academic_year_batch_with_non_array_schools(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => 'not_an_array',
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['message'])->toContain('Parâmetros de escola e períodos devem ser arrays');
    }

    public function test_process_academic_year_batch_with_non_array_periodos(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => 'not_an_array',
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['message'])->toContain('Parâmetros de escola e períodos devem ser arrays');
    }

    public function test_process_academic_year_batch_with_invalid_school(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [999999],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['errors'])->toBeArray();
        expect($result['errors'])->not()->toBeEmpty();
    }

    public function test_process_academic_year_batch_with_date_conflict(): void
    {
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
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('failed');
        expect($result['errors'])->toBeArray();
        expect($result['errors'])->not()->toBeEmpty();
    }

    public function test_process_academic_year_batch_with_existing_academic_year(): void
    {
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
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(0);
        expect($result['skipped'])->toBe(1);
        expect($result['details'])->toHaveCount(1);
        expect($result['details'][0]['type'])->toBe('skipped');
        expect($result['details'][0]['message'])->toContain('já está aberto');
    }

    public function test_process_academic_year_batch_successful(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['skipped'])->toBe(0);
        expect($result['details'])->toHaveCount(1);
        expect($result['details'][0]['type'])->toBe('success');
        expect($result['details'][0]['message'])->toContain('criado com sucesso');
    }

    public function test_process_academic_year_batch_with_multiple_schools_mixed_results(): void
    {
        $school2 = $this->createAdditionalSchool();
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
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

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
    }

    public function test_process_academic_year_batch_with_periods_filtering(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '', 'data_fim' => ''],
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
                ['data_inicio' => '', 'data_fim' => ''],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['skipped'])->toBe(0);
    }

    public function test_process_academic_year_batch_with_copy_classes_data(): void
    {
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();

        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['details'][0]['message'])->toContain('copiadas 1 turma(s)');
    }

    public function test_process_academic_year_batch_with_copy_teacher_data(): void
    {
        $this->createPreviousAcademicYear();
        $employeeData = $this->createEmployeeWithRole(isProfessor: true);
        $this->createEmployeeAllocation($employeeData);

        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => true,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) de professor(es)');
    }

    public function test_process_academic_year_batch_with_copy_employee_data(): void
    {
        $this->createPreviousAcademicYear();
        $employeeData = $this->createEmployeeWithRole(isProfessor: false);
        $this->createEmployeeAllocation($employeeData);

        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => true,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) dos demais servidor(es)');
    }

    public function test_process_academic_year_batch_with_all_copy_options(): void
    {
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();
        $teacherData = $this->createEmployeeWithRole(isProfessor: true);
        $employeeData = $this->createEmployeeWithRole(isProfessor: false);
        $this->createEmployeeAllocation($teacherData);
        $this->createEmployeeAllocation($employeeData);

        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => true,
            'copyEmployeeData' => true,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['details'][0]['message'])->toContain('copiadas 1 turma(s)');
        expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) de professor(es)');
        expect($result['details'][0]['message'])->toContain('copiadas 1 alocação(ões) dos demais servidor(es)');
    }

    public function test_process_academic_year_batch_with_no_copy_options(): void
    {
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();
        $employeeData = $this->createEmployeeWithRole(isProfessor: true);
        $this->createEmployeeAllocation($employeeData);

        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => $this->stageType->cod_modulo,
            'user' => $this->createUser(),
            'copySchoolClasses' => false,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
        expect($result['details'][0]['message'])->not()->toContain('copiadas');
    }

    public function test_process_academic_year_batch_with_invalid_module(): void
    {
        $params = [
            'year' => $this->currentYear,
            'schools' => [$this->school->cod_escola],
            'periodos' => [
                ['data_inicio' => '01/02/' . $this->currentYear, 'data_fim' => '30/04/' . $this->currentYear],
            ],
            'moduleId' => 30000,
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        expect(fn () => $this->service->processAcademicYearBatch($params))->toThrow(QueryException::class);
    }

    public function test_process_academic_year_batch_validates_stage_count(): void
    {
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
            'user' => $this->createUser(),
            'copySchoolClasses' => true,
            'copyTeacherData' => false,
            'copyEmployeeData' => false,
        ];

        $result = $this->service->processAcademicYearBatch($params);

        expect($result['status'])->toBe('completed');
        expect($result['processed'])->toBe(1);
    }

    public function test_validate_academic_year_dates_throws_exception_with_invalid_dates(): void
    {
        expect(fn () => $this->service->validateAcademicYearDates(
            ['invalid_date'],
            ['30/04/' . $this->currentYear],
            $this->currentYear,
            $this->school->cod_escola
        ))->toThrow(\Exception::class);
    }

    public function test_validate_stage_count_with_module_throws_exception_with_invalid_module(): void
    {
        expect(fn () => $this->service->validateStageCountWithModule(
            999999,
            1
        ))->toThrow(\Exception::class);
    }

    public function test_create_academic_year_for_school_throws_exception_with_invalid_module(): void
    {
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
            userId: $this->createUser()->id
        ))->toThrow(\Exception::class);
    }

    public function test_update_academic_year_stages_throws_exception_with_invalid_school(): void
    {
        expect(fn () => $this->service->updateAcademicYearStages(
            schoolId: 999999,
            year: $this->currentYear,
            startDates: ['01/02/' . $this->currentYear],
            endDates: ['30/04/' . $this->currentYear],
            schoolDays: [90],
            moduleId: $this->stageType->cod_modulo
        ))->toThrow(\Exception::class);
    }

    public function test_execute_copy_academic_years_copies_data_successfully(): void
    {
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
    }

    public function test_process_academic_year_batch_with_copy_school_classes_data(): void
    {
        $this->createPreviousAcademicYear();
        $this->createDefaultClass();

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
            'user' => $this->createUser(),
        ];

        $result = $this->service->processAcademicYearBatch($params);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEquals(0, $result['skipped']);
    }
}
