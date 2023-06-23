<?php

namespace Database\Factories;

use App\Models\LegacyCourseEducacensoStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCourseEducacensoStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyCourseEducacensoStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'etapa_id' => fn () => LegacyEducacensoStagesFactory::new()->create(),
            'curso_id' => fn () => LegacyCourseFactory::new()->create(),
        ];
    }
}
