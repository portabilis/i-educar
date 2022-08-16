<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyAbandonmentType>
 */
class LegacyAbandonmentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'nome' => $this->faker->firstName(),
        ];
    }
}
