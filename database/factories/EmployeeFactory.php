<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => fn () => LegacyPersonFactory::new()->create(),
            'institution_id' => fn () => LegacyInstitutionFactory::new()->current(),
            'workload' => 40,
            'ref_idesco' => fn () => LegacySchoolingDegreeFactory::new()->unique()->make(),
            'curso_formacao_continuada' => '{1}',
        ];
    }

    public function current(): Employee
    {
        return Employee::query()->first() ?? $this->create([
            'id' => fn () => LegacyPersonFactory::new()->current(),
        ]);
    }

    public function withTeacherFunction(): static
    {
        return $this->afterCreating(function (Employee $employee) {
            LegacyEmployeeRoleFactory::new()->create([
                'ref_cod_servidor' => $employee,
            ]);
        });
    }
}
