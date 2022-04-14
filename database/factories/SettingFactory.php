<?php

namespace Database\Factories;

use App\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([
            Setting::TYPE_STRING,
            Setting::TYPE_INTEGER,
            Setting::TYPE_FLOAT,
            Setting::TYPE_BOOLEAN,
        ]);

        if ($type === Setting::TYPE_STRING) {
            $value = $this->faker->words(3, true);
        } elseif ($type === Setting::TYPE_INTEGER) {
            $value = $this->faker->numberBetween(0, 1000000);
        } elseif ($type === Setting::TYPE_FLOAT) {
            $value = $this->faker->randomFloat(2, 10, 100);
        } elseif ($type === Setting::TYPE_BOOLEAN) {
            $value = $this->faker->boolean;
        }

        return [
            'key' => $this->faker->unique()->word,
            'value' => $value,
            'type' => $this->faker->randomElement(['string', 'integer', 'float', 'boolean']),
            'description' => $this->faker->words(3, true),
        ];
    }
}
