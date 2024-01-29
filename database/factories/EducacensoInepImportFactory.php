<?php

namespace Database\Factories;

use App\Models\EducacensoInepImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducacensoInepImportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducacensoInepImport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => now()->year,
            'school_name' => $this->faker->name,
            'user_id' => fn () => LegacyUserFactory::new()->current(),
        ];
    }
}
