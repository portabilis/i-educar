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
            'institution_id' => fn () => LegacyInstitutionFactory::new()->unique()->make(),
            'knowledge_area_id' => LegacyKnowledgeAreaFactory::new()->unique()->make(),
            'name' => $this->faker->colorName,
            'abbreviation' => $this->faker->hexColor,
            'tipo_base' => 0
        ];
    }
}
