<?php

namespace Database\Factories;

use App\Models\LegacyUserType;
use App_Model_NivelTipoUsuario;
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
     */
    public function definition(): array
    {
        return [
            'nm_tipo' => $this->faker->firstName(),
            'nivel' => App_Model_NivelTipoUsuario::INSTITUCIONAL,
            'ref_funcionario_cad' => fn () => LegacyEmployeeFactory::new()->current()->ref_cod_pessoa_fj,
            'data_cadastro' => now(),
        ];
    }
}
