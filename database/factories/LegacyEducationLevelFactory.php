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
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'nm_nivel' => $this->faker->firstName(),
            'descricao' => $this->faker->paragraph(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
