<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = State::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'country_id' => CountryFactory::new()->create(),
            'name' => Str::ucfirst($month = $this->faker->monthName()) . ' ' . Str::ucfirst($color = $this->faker->colorName()),
            'abbreviation' => Str::substr($month, 0, 1) . ' ' . Str::substr($color, 0, 1),
            'ibge_code' => $this->faker->numerify('########'),
        ];
    }
}
