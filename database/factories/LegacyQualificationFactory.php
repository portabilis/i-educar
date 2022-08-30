<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyQualification>
 */
class LegacyQualificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'nm_tipo' => $this->faker->firstName(),
            'descricao' => $this->faker->paragraph(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
