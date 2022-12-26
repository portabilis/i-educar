<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        EnrollmentFactory::new()->make();

        $instance = new $this->model();

        return $instance->query()->first()->getAttributes();
    }
}
