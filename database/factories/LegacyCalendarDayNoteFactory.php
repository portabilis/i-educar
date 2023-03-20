<?php

namespace Database\Factories;

use App\Models\LegacyCalendarDayNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCalendarDayNoteFactory extends Factory
{
    public $model = LegacyCalendarDayNote::class;

    public function definition()
    {
        return [
            'ref_dia' => now()->day,
            'ref_mes' => now()->month,
            'ref_ref_cod_calendario_ano_letivo' => fn () => LegacyCalendarYearFactory::new()->create(),
            'ref_cod_calendario_anotacao' => fn () => LegacyCalendarDayNoteFactory::new()->create(),
        ];
    }
}
