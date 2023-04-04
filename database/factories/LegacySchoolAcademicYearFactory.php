<?php

namespace Database\Factories;

use App\Models\LegacySchoolAcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolAcademicYearFactory extends Factory
{
    protected $model = LegacySchoolAcademicYear::class;

    public function definition(): array
    {
        return [
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ano' => now()->year,
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'andamento' => 1
        ];
    }
}
