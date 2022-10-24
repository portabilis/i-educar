<?php

namespace Database\Factories;

use App\Models\StudentInep;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StudentInepFactory extends Factory
{
    protected $model = StudentInep::class;

    public function definition(): array
    {
        return [
            'cod_aluno_inep' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'cod_aluno' => static fn () => LegacyStudentFactory::new()->create(),
        ];
    }
}
