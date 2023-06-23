<?php

namespace Database\Factories;

use App\Models\LegacyBenefit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyBenefit>
 */
class LegacyBenefitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyBenefit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_by' => fn () => LegacyUserFactory::new()->current(),
            'deleted_by' => fn () => LegacyUserFactory::new()->current(),
            'name' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
