<?php

namespace Database\Factories;

use App\Models\LegacyCalendarYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCalendarYearFactory extends Factory
{
    public $model = LegacyCalendarYear::class;

    public function definition()
    {
        return [
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ano' => now()->year,
        ];
    }
}
