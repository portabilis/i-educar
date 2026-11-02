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
     */
    public function definition(): array
    {
        // A FK de servidor_afastamento é composta por (ref_cod_servidor,
        // ref_ref_cod_instituicao), então a instituição precisa vir do próprio
        // servidor: resolvê-la à parte gera um par que não existe em servidor.
        $employee = EmployeeFactory::new()->create();

        return [
            'ref_cod_servidor' => $employee,
            'sequencial' => $this->faker->randomDigitNotZero(),
            'ref_ref_cod_instituicao' => $employee->institution_id,
            'ref_cod_motivo_afastamento' => WithdrawalReasonFactory::new()->create(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'data_retorno' => now(),
            'data_saida' => now(),
        ];
    }
}
