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
     */
    public function definition(): array
    {
        return [
            'ref_cod_transferencia_tipo' => fn () => LegacyTransferTypeFactory::new()->create(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_matricula_entrada' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_matricula_saida' => fn () => LegacyRegistrationFactory::new()->create(),
            'observacao' => $this->faker->text,
            'ativo' => 1,
            'data_transferencia' => now(),
            'ref_cod_escola_destino' => fn () => LegacySchoolFactory::new()->create(),
        ];
    }
}
