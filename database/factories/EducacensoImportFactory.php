<?php

namespace Database\Factories;

use App\Models\EducacensoImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducacensoImportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducacensoImport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => now()->year,
            'school' => $this->faker->name(),
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'finished' => $this->faker->boolean,
            'registration_date' => now()->format('Y-m-d'),
        ];
    }
}
