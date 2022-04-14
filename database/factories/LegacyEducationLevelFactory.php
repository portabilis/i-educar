<?php

namespace Database\Factories;

use App\Models\LegacyEducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEducationLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEducationLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => 1,
            'nm_nivel' => $this->faker->word,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
