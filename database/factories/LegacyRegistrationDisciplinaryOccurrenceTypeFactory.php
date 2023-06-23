<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyRegistrationDisciplinaryOccurrenceType>
 */
class LegacyRegistrationDisciplinaryOccurrenceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_tipo_ocorrencia_disciplinar' => fn () => LegacyDisciplinaryOccurrenceTypeFactory::new()->create(),
            'sequencial' => 1,
            'ref_usuario_exc' => null,
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'observacao' => $this->faker->paragraph(),
            'data_exclusao' => null,
            'visivel_pais' => fn () => $this->faker->boolean(),
        ];
    }
}
