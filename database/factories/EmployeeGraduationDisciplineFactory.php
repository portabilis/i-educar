<?php

namespace Database\Factories;

use App\Models\EmployeeGraduationDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeGraduationDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeGraduationDiscipline::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
