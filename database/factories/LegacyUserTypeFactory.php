<?php

namespace Database\Factories;

use App\Models\LegacyUserType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyUserTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyUserType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nm_tipo' => $this->faker->firstName,
            'nivel' => $this->faker->randomElement([1, 2, 3, 4]),
            'ref_funcionario_cad' => function () {
                return LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj;
            },
            'data_cadastro' => $this->faker->dateTime,
        ];
    }
}
