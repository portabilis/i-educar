<?php

namespace Database\Factories;

use App\Models\LegacyStudentScore;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentScoreFactory extends Factory
{
    protected $model = LegacyStudentScore::class;

    public function definition(): array
    {
        return [
            'matricula_id' => fn () => LegacyRegistrationFactory::new()->create(),
        ];
    }
}
