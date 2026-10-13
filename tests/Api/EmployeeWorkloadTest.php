<?php

namespace Tests\Api;

use App\Models\Employee;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EmployeeWorkloadTest extends TestCase
{
    use DatabaseTransactions;

    private const ROUTE = '/intranet/educar_servidor_cad.php';

    public function test_employee_workload_can_be_updated_with_valid_value(): void
    {
        $employee = $this->employeeScenario();

        $this->post(self::ROUTE, $this->editPayload($employee, '40:30'))
            ->assertRedirectContains('educar_servidor_det.php');

        $this->assertDatabaseHas($employee->getTable(), [
            'cod_servidor' => $employee->getKey(),
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
            'carga_horaria' => 40.5,
            'ativo' => 1,
        ]);
    }

    public function test_workload_99_59_can_be_created(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $individual = LegacyIndividualFactory::new()->create();
        $this->actingAs($user);

        $this->post(self::ROUTE, [
            'tipoacao' => 'Novo',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'carga_horaria' => '99:59',
        ])->assertRedirectContains('educar_servidor_det.php');

        $this->assertEqualsWithDelta(99 + 59 / 60, Employee::query()
            ->whereEmployee($individual->getKey())
            ->whereInstitution($user->ref_cod_instituicao)
            ->firstOrFail()->carga_horaria, 0.00001);
    }

    public function test_workload_99_59_can_be_updated(): void
    {
        $employee = $this->employeeScenario();

        $this->post(self::ROUTE, $this->editPayload($employee, '99:59'))
            ->assertRedirectContains('educar_servidor_det.php');

        $this->assertEqualsWithDelta(99 + 59 / 60, $employee->fresh()->carga_horaria, 0.00001);
    }

    #[DataProvider('invalidWorkloads')]
    public function test_invalid_workload_cannot_be_created(string $workload): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $individual = LegacyIndividualFactory::new()->create();
        $this->actingAs($user);

        $this->post(self::ROUTE, [
            'tipoacao' => 'Novo',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'carga_horaria' => $workload,
        ])->assertOk()->assertSee('Carga horária inválida');

        $this->assertDatabaseMissing((new Employee)->getTable(), [
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ]);
    }

    #[DataProvider('invalidWorkloads')]
    public function test_invalid_workload_cannot_be_updated(string $workload): void
    {
        $employee = $this->employeeScenario();

        $this->post(self::ROUTE, $this->editPayload($employee, $workload))
            ->assertOk()->assertSee('Carga horária inválida');

        $this->assertSame(20.0, (float) $employee->fresh()->carga_horaria);
    }

    public static function invalidWorkloads(): array
    {
        return [
            '100:00' => ['100:00'],
            '100:39' => ['100:39'],
        ];
    }

    private function employeeScenario(): Employee
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $individual = LegacyIndividualFactory::new()->create();

        $this->actingAs($user);

        return EmployeeFactory::new()->create([
            'id' => $individual->getKey(),
            'institution_id' => $user->ref_cod_instituicao,
            'workload' => 20,
        ]);
    }

    private function editPayload(Employee $employee, string $workload): array
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
}
