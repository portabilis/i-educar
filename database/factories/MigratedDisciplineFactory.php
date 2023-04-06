<?php

namespace Database\Factories;

use App\Models\MigratedDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MigratedDisciplineFactory extends Factory
{
    protected $model = MigratedDiscipline::class;

    public function definition(): array
    {
        return [
            'old_discipline_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'new_discipline_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'grade_id' => fn () => LegacyGradeFactory::new()->create(),
            'year' => Carbon::now()->year,
            'created_by' => fn () => LegacyUserFactory::new()->current(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
