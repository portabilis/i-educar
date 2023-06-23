<?php

namespace Database\Factories;

use App\Models\LegacyCalendarDayReason;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCalendarDayReasonFactory extends Factory
{
    public $model = LegacyCalendarDayReason::class;

    public function definition()
    {
        return [
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'sigla' => $this->faker->word,
            'descricao' => $this->faker->sentence,
            'tipo' => $this->faker->randomElement(['e', 'n']),
            'nm_motivo' => $this->faker->word,
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
        ];
    }
}
