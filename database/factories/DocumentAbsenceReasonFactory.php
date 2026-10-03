<?php

namespace Database\Factories;

use App\Models\DocumentAbsenceReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentAbsenceReason>
 */
class DocumentAbsenceReasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(),
            'requires_observation' => $this->faker->boolean(),
            'from_system' => false,
        ];
    }
}
