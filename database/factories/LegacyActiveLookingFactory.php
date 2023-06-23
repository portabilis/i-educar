<?php

namespace Database\Factories;

use App\Models\LegacyActiveLooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyActiveLookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyActiveLooking::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'registration_id' => fn () => LegacyRegistrationFactory::new()->create(),
            'start' => now()->subDays(7),
            'end' => now(),
            'obs' => $this->faker->paragraph,
            'result' => $this->faker->randomDigitNotZero(),
        ];
    }
}
