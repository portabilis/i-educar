<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyDisciplinaryOccurrenceType>
 */
class LegacyDisciplinaryOccurrenceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'nm_tipo' => $this->faker->firstName(),
            'descricao' => $this->faker->paragraph(),
            'max_ocorrencias' => $this->faker->numberBetween(1, 5),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->unique()->make()
        ];
    }
}
