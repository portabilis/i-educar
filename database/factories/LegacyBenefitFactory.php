<?php

namespace Database\Factories;

use App\Models\LegacyBenefit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyBenefit>
 */
class LegacyBenefitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyBenefit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'nm_beneficio' => $this->faker->firstName(),
            'desc_beneficio' => $this->faker->paragraph(),
            'data_cadastro' => now()
        ];
    }
}
