<?php

namespace Database\Factories;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolAcademicYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolAcademicYear::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_escola' => LegacySchool::factory()->create(),
            'ano' => now()->year,
            'ref_usuario_cad' => LegacyUser::factory()->unique()->make(),
            'andamento' => 1,
            'data_cadastro' => now(),
        ];
    }
}
