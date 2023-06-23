<?php

namespace Database\Factories;

use App\Models\StudentInep;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentInepFactory extends Factory
{
    protected $model = StudentInep::class;

    public function definition(): array
    {
        return [
            'student_id' => fn () => LegacyStudentFactory::new()->create(),
            'number' => $this->faker->randomNumber(),
            'name' => $this->faker->word(),
            'font' => $this->faker->word(),
        ];
    }
}
