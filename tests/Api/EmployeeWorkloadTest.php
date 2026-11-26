<?php

use App\Models\Employee;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

const EMPLOYEE_WORKLOAD_ROUTE = '/intranet/educar_servidor_cad.php';

test('employee workload can be updated with valid value', function () {
    $employee = employeeWorkloadScenario($this);

    $this->post(EMPLOYEE_WORKLOAD_ROUTE, employeeWorkloadEditPayload($employee, '40:30'))
        ->assertRedirectContains('educar_servidor_det.php');

    $this->assertDatabaseHas($employee->getTable(), [
        'cod_servidor' => $employee->getKey(),
        'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        'carga_horaria' => 40.5,
        'ativo' => 1,
    ]);
});

test('workload 99 59 can be created', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $individual = LegacyIndividualFactory::new()->create();
    $this->actingAs($user);

    $this->post(EMPLOYEE_WORKLOAD_ROUTE, [
        'tipoacao' => 'Novo',
        'cod_servidor' => $individual->getKey(),
        'ref_cod_instituicao' => $user->ref_cod_instituicao,
        'carga_horaria' => '99:59',
    ])->assertRedirectContains('educar_servidor_det.php');

    expect(Employee::query()
        ->whereEmployee($individual->getKey())
        ->whereInstitution($user->ref_cod_instituicao)
        ->firstOrFail()->carga_horaria)->toEqualWithDelta(99 + 59 / 60, 0.00001);
});

test('workload 99 59 can be updated', function () {
    $employee = employeeWorkloadScenario($this);

    $this->post(EMPLOYEE_WORKLOAD_ROUTE, employeeWorkloadEditPayload($employee, '99:59'))
        ->assertRedirectContains('educar_servidor_det.php');

    expect($employee->fresh()->carga_horaria)->toEqualWithDelta(99 + 59 / 60, 0.00001);
});

test('invalid workload cannot be created', function (string $workload) {
    $user = LegacyUserFactory::new()->admin()->create();
    $individual = LegacyIndividualFactory::new()->create();
    $this->actingAs($user);

    $this->post(EMPLOYEE_WORKLOAD_ROUTE, [
        'tipoacao' => 'Novo',
        'cod_servidor' => $individual->getKey(),
        'ref_cod_instituicao' => $user->ref_cod_instituicao,
        'carga_horaria' => $workload,
    ])->assertOk()->assertSee('Carga horária inválida');

    $this->assertDatabaseMissing((new Employee)->getTable(), [
        'cod_servidor' => $individual->getKey(),
        'ref_cod_instituicao' => $user->ref_cod_instituicao,
    ]);
})->with('invalidEmployeeWorkloads');

test('invalid workload cannot be updated', function (string $workload) {
    $employee = employeeWorkloadScenario($this);

    $this->post(EMPLOYEE_WORKLOAD_ROUTE, employeeWorkloadEditPayload($employee, $workload))
        ->assertOk()->assertSee('Carga horária inválida');

    expect((float) $employee->fresh()->carga_horaria)->toBe(20.0);
})->with('invalidEmployeeWorkloads');

dataset('invalidEmployeeWorkloads', function () {
    return [
        '100:00' => ['100:00'],
        '100:39' => ['100:39'],
    ];
});

function employeeWorkloadScenario(TestCase $test): Employee
{
    $user = LegacyUserFactory::new()->admin()->create();
    $individual = LegacyIndividualFactory::new()->create();

    $test->actingAs($user);

    return EmployeeFactory::new()->create([
        'id' => $individual->getKey(),
        'institution_id' => $user->ref_cod_instituicao,
        'workload' => 20,
    ]);
}

function employeeWorkloadEditPayload(Employee $employee, string $workload): array
{
    return [
        'tipoacao' => 'Editar',
        'cod_servidor' => $employee->getKey(),
        'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        'ref_cod_instituicao_original' => $employee->ref_cod_instituicao,
        'ref_idesco' => $employee->ref_idesco,
        'carga_horaria' => $workload,
    ];
}
