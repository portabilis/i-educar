<?php

namespace Database\Factories;

use App\Models\EmployeeWithdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeWithdrawalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeWithdrawal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_servidor' => EmployeeFactory::new()->create(),
            'sequencial' => $this->faker->randomDigitNotZero(),
            'ref_ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_cod_motivo_afastamento' => WithdrawalReasonFactory::new()->create(),
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => LegacyUserFactory::new()->current(),
            'data_retorno' => now(),
            'data_saida' => now(),
        ];
    }
}
