<?php

namespace Database\Factories;

use App\Models\LegacyLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nm_serie' => $this->faker->words(3, true),
            'ref_usuario_cad' => 1,
            'ref_cod_curso' => LegacyCourseFactory::new()->create(),
            'etapa_curso' => $this->faker->randomElement([1, 2, 3, 4]),
            'carga_horaria' => 800,
            'data_cadastro' => $this->faker->dateTime(),
        ];
    }
}
