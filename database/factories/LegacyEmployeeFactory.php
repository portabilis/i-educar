<?php

namespace Database\Factories;

use App\Models\LegacyEmployee;
use App\Models\LegacyIndividual;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEmployee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_pessoa_fj' => function () {
                return LegacyIndividual::factory()->create()->idpes;
            },
            'matricula' => $this->faker->randomDigitNotNull(),
            'senha' => $this->faker->randomDigitNotNull(),
            'ativo' => 1
        ];
    }
}
