<?php

namespace Database\Factories;

use App\Models\LegacyProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyProject>
 */
class LegacyProjectFactory extends Factory
{
    protected $model = LegacyProject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->firstName(),
            'observacao' => $this->faker->text(200),
        ];
    }
}
