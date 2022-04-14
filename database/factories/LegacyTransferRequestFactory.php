<?php

namespace Database\Factories;

use App\Models\LegacyTransferRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyTransferRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyTransferRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => 1,
            'observacao' => $this->faker->words(3, true),
            'ref_cod_escola_destino' => LegacySchoolFactory::new()->create(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'ref_cod_transferencia_tipo' => LegacyTransferTypeFactory::new()->create()
        ];
    }
}
