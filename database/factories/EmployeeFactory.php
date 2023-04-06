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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id' => fn () => LegacyPersonFactory::new()->create(),
            'institution_id' => fn () => LegacyInstitutionFactory::new()->current(),
            'workload' => $this->faker->randomNumber(3),
            'ref_idesco' => fn () => LegacySchoolingDegreeFactory::new()->unique()->make(),
            'curso_formacao_continuada' => '{1}'
        ];
    }
}
