<?php

namespace Database\Factories;

use App\Models\LegacyRace;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRace::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'idpes_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_raca' => $this->faker->colorName(),
            'raca_educacenso' => random_int(0, 5),
        ];
    }
}
