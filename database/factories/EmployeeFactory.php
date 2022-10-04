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
            'cod_servidor' => LegacyPersonFactory::new()->create(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'carga_horaria' => $this->faker->randomNumber(3),
        ];
    }
}
