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
     */
    public function definition(): array
    {
        return [
            'nm_tipo' => $this->faker->firstName(),
            'descricao' => $this->faker->paragraph(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }
}
