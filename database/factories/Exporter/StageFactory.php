<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Stage;
use Database\Factories\LegacyAcademicYearStageFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        LegacyAcademicYearStageFactory::new()->create();
        $instance = new $this->model();

        return $instance->query()->first()->getAttributes();
    }
}
