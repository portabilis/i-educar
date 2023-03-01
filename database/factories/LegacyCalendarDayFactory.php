<?php

namespace Database\Factories;

use App\Models\LegacyCalendarDay;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCalendarDayFactory extends Factory
{
    protected $model = LegacyCalendarDay::class;

    public function definition()
    {
        return [
            'ref_usuario_exc' =>  fn () => LegacyUserFactory::new()->unique()->make(),
            'ref_usuario_cad' =>  fn () => LegacyUserFactory::new()->unique()->make(),
            //'data_cadastro',
            //'data_exclusao',
            'ativo' => 1,
            'ref_cod_calendario_ano_letivo' => fn () => LegacyCalendarYearFactory::new()->create(),
            'mes' => now()->month,
            'dia' => now()->day,
            'ref_cod_calendario_dia_motivo' => fn () => LegacyCalendarDayReasonFactory::new()->create(),
            'descricao' => $this->faker->words(3, true),
        ];
    }
}
