<?php

namespace Database\Factories;

use App\Models\EmployeeGraduation;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeGraduationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeGraduation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'employee_id' => EmployeeFactory::new()->create(),
            'course_id' => EducacensoDegreeFactory::new()->create(),
            'completion_year' => now()->year,
            'college_id' => EducacensoInstitutionFactory::new()->create(),
            'discipline_id' => EmployeeGraduationDisciplineFactory::new()->create(),
        ];
    }
}
