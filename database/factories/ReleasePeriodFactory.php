<?php

namespace Database\Factories;

use App\Models\ReleasePeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReleasePeriodFactory extends Factory
{
    protected $model = ReleasePeriod::class;

    public function definition(): array
    {
        return [
            'year' => Carbon::today()->year,
            'stage' => $this->faker->randomNumber(),
            'stage_type_id' => fn () => LegacyStageTypeFactory::new()->create(),
        ];
    }
}
