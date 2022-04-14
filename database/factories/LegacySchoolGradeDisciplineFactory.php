<?php

namespace Database\Factories;

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
            'ref_ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_ref_cod_serie' => LegacyLevelFactory::new()->create(),
            'ref_cod_disciplina' => LegacyDisciplineFactory::new()->create(),
            'ativo' => 1,
            'anos_letivos' => '{' . now()->year . '}',
        ];
    }
}
