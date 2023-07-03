<?php

namespace Database\Factories;

use App\Models\LegacySchoolingDegree;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolingDegreeFactory extends Factory
{
    protected $model = LegacySchoolingDegree::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->name(),
            'schooling' => 1,
        ];
    }

    public function unique(): self
    {
        return $this->state(function () {
            $schooling = LegacySchoolingDegree::query()->first();

            if (empty($schooling)) {
                $schooling = LegacySchoolingDegreeFactory::new()->create();
            }

            return [
                'id' => $schooling->getKey(),
            ];
        });
    }
}
