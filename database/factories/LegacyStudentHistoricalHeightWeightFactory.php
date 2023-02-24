<?php

namespace Database\Factories;

use App\Models\LegacyStudentHistoricalHeightWeight;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentHistoricalHeightWeightFactory extends Factory
{
    protected $model = LegacyStudentHistoricalHeightWeight::class;

    public function definition()
    {
        return [
            'ref_cod_aluno' => fn () => LegacyStudentFactory::new()->create(),
            'data_historico' => $this->faker->date(),
            'altura' => $this->faker->randomFloat(2, 0, 2),
            'peso' => $this->faker->randomFloat(2, 0, 2),
        ];
    }
}
