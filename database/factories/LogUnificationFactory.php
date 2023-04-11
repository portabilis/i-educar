<?php

namespace Database\Factories;

use App\Models\LogUnification;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogUnificationFactory extends Factory
{
    protected $model = LogUnification::class;

    public function definition(): array
    {
        return [
            'type' => Student::class,
            'duplicates_id' => '[]',
            'active' => $this->faker->boolean(),
            'main_id' => fn () => LegacyStudentFactory::new()->create(),
            'created_by' => fn () => LegacyIndividualFactory::new()->create(),
            'updated_by' => fn () => LegacyIndividualFactory::new()->create(),
        ];
    }
}
