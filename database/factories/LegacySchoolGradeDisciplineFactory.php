<?php

namespace Database\Factories;

use App\Models\LegacyDiscipline;
use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolGradeDiscipline::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_ref_cod_escola' => LegacySchool::factory()->create(),
            'ref_ref_cod_serie' => LegacyLevel::factory()->create(),
            'ref_cod_disciplina' => LegacyDiscipline::factory()->create(),
            'ativo' => 1,
            'anos_letivos' => '{' . now()->year . '}',
        ];
    }
}
