<?php

namespace Database\Factories;

use App\Models\LegacyEducationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEducationTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEducationType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'nm_tipo' => $this->faker->firstName(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current(),
            'atividade_complementar' => $this->faker->boolean(),
        ];
    }
}
