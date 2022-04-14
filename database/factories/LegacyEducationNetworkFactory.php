<?php

namespace Database\Factories;

use App\Models\LegacyEducationNetwork;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEducationNetworkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEducationNetwork::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'nm_rede' => $this->faker->company,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }
}
