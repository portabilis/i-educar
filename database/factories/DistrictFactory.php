<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = District::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'city_id' => CityFactory::new()->create(),
            'name' => $this->faker->dayOfWeek() . ' District',
            'ibge_code' => $this->faker->numerify('########'),
        ];
    }
}
