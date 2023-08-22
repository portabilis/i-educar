<?php

namespace Database\Factories;

use App\Models\PerformanceEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceEvaluationFactory extends Factory
{
    protected $model = PerformanceEvaluation::class;

    public function definition()
    {
        return [
            'sequential' => $this->faker->randomNumber(),
            'employee_id' => EmployeeFactory::new()->create(),
            'institution_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'description' => $this->faker->text(),
            'title' => $this->faker->text(),
            'deleted_by' => LegacyUserFactory::new()->unique()->make(),
            'created_by' => LegacyUserFactory::new()->unique()->make(),
        ];
    }
}
