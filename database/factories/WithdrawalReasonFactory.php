<?php

namespace Database\Factories;

use App\Models\WithdrawalReason;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalReasonFactory extends Factory
{
    protected $model = WithdrawalReason::class;

    public function definition(): array
    {
        return [
            'ref_usuario_exc' => fn () => LegacyUserFactory::new(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new(),
            'nm_motivo' => $this->faker->word(),
            'descricao' => $this->faker->text(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new(),
        ];
    }
}
