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
     *
     * @return array
     */
    public function definition(): array
    {
        $exemption = LegacyDisciplineExemptionFactory::new()->create();

        return [
            'ref_cod_dispensa' => $exemption->cod_dispensa,
            'etapa' => 1,
        ];
    }
}
