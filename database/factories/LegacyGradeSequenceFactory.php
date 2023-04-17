<?php

namespace Database\Factories;

use App\Models\LegacyGradeSequence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LegacyGradeSequence>
 */
class LegacyGradeSequenceFactory extends Factory
{
    protected $model = LegacyGradeSequence::class;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_serie_origem' => fn () => LegacyGradeFactory::new()->create(),
            'ref_serie_destino' => fn () => LegacyGradeFactory::new()->create(),
            'ativo' => 1,
            'data_cadastro' => now(),
        ];
    }
}
