<?php

namespace Database\Factories;

use App\Models\LegacyDisciplinaryOccurrenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyDisciplinaryOccurrenceType>
 */
class LegacyDisciplinaryOccurrenceTypeFactory extends Factory
{
    protected $model = LegacyDisciplinaryOccurrenceType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_by' => fn () => LegacyUserFactory::new()->current(),
            'deleted_by' => fn () => LegacyUserFactory::new()->current(),
            'name' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(),
            'max' => $this->faker->numberBetween(1, 5),
            'institution_id' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }
}
