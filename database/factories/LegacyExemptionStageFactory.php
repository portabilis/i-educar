<?php

namespace Database\Factories;

use App\Models\LegacyExemptionStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyExemptionStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyExemptionStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_cod_dispensa' => fn () => LegacyDisciplineExemptionFactory::new()->create(),
            'etapa' => 1,
        ];
    }
}
