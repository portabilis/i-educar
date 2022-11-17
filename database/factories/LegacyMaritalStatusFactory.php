<?php

namespace Database\Factories;

use App\Models\LegacyMaritalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyMaritalStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyMaritalStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'descricao' => $this->faker->unique()->word,
        ];
    }
}
