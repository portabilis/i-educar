<?php

namespace Database\Factories;

use App\Models\ReleasePeriodDate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReleasePeriodDateFactory extends Factory
{
    protected $model = ReleasePeriodDate::class;

    public function definition(): array
    {
        return [
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDays(10),
            'release_period_id' => ReleasePeriodFactory::new()->create(),
        ];
    }
}
