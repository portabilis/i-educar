<?php

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\EmployeeGraduation;
use App\Models\EmployeeInep;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;
use App\Models\LegacySchoolingDegree;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\EmployeeInepFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacySchoolFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    Employee::class,
    relations: [
        'person' => LegacyPerson::class,
        'employeeAllocations' => EmployeeAllocation::class,
        'employeeRoles' => LegacyEmployeeRole::class,
        'graduations' => EmployeeGraduation::class,
        'schoolingDegree' => LegacySchoolingDegree::class,
        'institution' => LegacyInstitution::class,
    ],
    legacyAttributes: [
        'id' => 'cod_servidor',
        'workload' => 'carga_horaria',
        'created_at' => 'data_cadastro',
        'institution_id' => 'ref_cod_instituicao',
    ],
));

EloquentModelTestCase::registerModelTests();

test('relationship inep', function () {
    $employee = EmployeeFactory::new()->create();
    $employee->inep = EmployeeInepFactory::new()->create([
        'cod_servidor' => $employee->id,
    ]);
    $this->assertInstanceOf(EmployeeInep::class, $employee->inep);
});

test('relationship individual', function () {
    $employee = EmployeeFactory::new()->create();
    $this->assertInstanceOf(LegacyIndividual::class, $employee->individual);
});

test('get id attribute', function () {
    $this->assertInstanceOf(Employee::class, $this->model);
    $this->assertIsInt($this->model->id);
    $this->assertEquals($this->model->cod_servidor, $this->model->id);
});

test('scope professor', function () {
    try {
        Employee::query()
            ->professor()
            ->first();
        $this->assertTrue(true);
    } catch (Exception $exception) {
        $this->fail('Exception thrown due to scope error');
    }
});

test('scope last year', function () {
    try {
        Employee::query()
            ->lastYear()
            ->first();

        $this->assertTrue(true);
    } catch (Exception $exception) {
        $this->fail('Exception thrown due to scope error');
    }
});

test('scope current year', function () {
    try {
        Employee::query()
            ->currentYear()
            ->first();

        $this->assertTrue(true);
    } catch (Exception $exception) {
        $this->fail('Exception thrown due to scope error');
    }
});

test('relationship schools', function () {
    EmployeeAllocationFactory::new()->create([
        'ref_cod_servidor' => $this->model,
        'ref_cod_escola' => LegacySchoolFactory::new()->create(),
    ]);

    $this->assertCount(1, $this->model->schools);
    $this->assertInstanceOf(LegacySchool::class, $this->model->schools->first());
});

test('relationship disciplines', function () {
    $employeeRole = LegacyEmployeeRoleFactory::new()->create();
    $course = LegacyCourseFactory::new()->create();

    $employee = EmployeeFactory::new()->hasAttached(LegacyDisciplineFactory::new(), [
        'ref_ref_cod_instituicao' => $this->model->institution_id,
        'ref_cod_curso' => $course->id,
        'ref_cod_funcao' => $employeeRole->id,
    ], 'disciplines')->create();

    $this->assertCount(1, $employee->disciplines);
    $this->assertInstanceOf(LegacyDiscipline::class, $employee->disciplines->first());
});

test('relationship courses', function () {
    $employee = EmployeeFactory::new()->hasAttached(LegacyCourseFactory::new(), [
        'ref_ref_cod_instituicao' => $this->model->institution_id,
    ], 'courses')->create();

    $this->assertCount(1, $employee->courses);
    $this->assertInstanceOf(LegacyCourse::class, $employee->courses->first());
});
