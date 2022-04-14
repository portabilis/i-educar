<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Place::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'city_id' => CityFactory::new()->create(),
            'address' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(1, 9999),
            'complement' => $this->faker->boolean ? 'Apto' : null,
            'neighborhood' => $this->faker->month . ' Neighborhood',
            'postal_code' => $this->faker->numerify('########'),
        ];
    }
}
