<?php

namespace Database\Factories;

use App\Models\SchoolInep;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolInepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolInep::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'cod_escola' => LegacySchoolFactory::new()->create(),
            'cod_escola_inep' => $this->faker->numerify('########'),
        ];
    }
}
