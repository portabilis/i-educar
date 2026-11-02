<?php

namespace Database\Factories;

use App\Models\LegacyStudentHousing;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentHousingFactory extends Factory
{
    protected $model = LegacyStudentHousing::class;

    public function definition(): array
    {
        return [
            'ref_cod_aluno' => fn () => LegacyStudentFactory::new()->create(),
            'moradia' => $this->faker->randomElement(['A', 'B', 'C']),
            'material' => 'A',
            'quartos' => $this->faker->numberBetween(1, 4),
            'quant_pessoas' => $this->faker->numberBetween(1, 8),
            'renda' => $this->faker->randomFloat(2, 500, 5000),
            'agua_encanada' => 'S',
            'energia' => 'S',
        ];
    }
}
