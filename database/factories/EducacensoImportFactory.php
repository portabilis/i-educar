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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'year' => now()->year,
            'school' => $this->faker->name(),
            'user_id' => LegacyUserFactory::new()->unique()->make(),
            'finished' => $this->faker->boolean,
            'registration_date' => now()->format('Y-m-d')
        ];
    }
}
