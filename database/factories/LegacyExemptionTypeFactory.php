<?php

namespace Database\Factories;

use App\Models\LegacyExemptionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyExemptionTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyExemptionType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nm_tipo' => $this->faker->words(2, true),
            'descricao' => $this->faker->words(5, true),
            'ativo' => 1,
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
