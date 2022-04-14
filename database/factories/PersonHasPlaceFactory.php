<?php

namespace Database\Factories;

use App\Models\PersonHasPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonHasPlaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonHasPlace::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'person_id' => LegacyPersonFactory::new()->create(),
            'place_id' => PlaceFactory::new()->create(),
            'type' => 1,
        ];
    }
}
