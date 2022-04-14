<?php

namespace Database\Factories;

use App\Models\LegacyDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDiscipline::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'instituicao_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'area_conhecimento_id' => LegacyKnowledgeAreaFactory::new()->unique()->make(),
            'nome' => $this->faker->colorName,
            'abreviatura' => $this->faker->hexColor,
            'tipo_base' => 0,
        ];
    }
}
