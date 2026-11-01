<?php

namespace Database\Factories;

use App\Models\SchoolSpace;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolSpaceFactory extends Factory
{
    protected $model = SchoolSpace::class;

    public function definition(): array
    {
        return [
            'school_id' => fn () => LegacySchoolFactory::new()->create(),
            'name' => $this->faker->words(2, true),
            'size' => $this->faker->numberBetween(20, 200),
        ];
    }
}
