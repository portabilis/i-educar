<?php

namespace Database\Factories;

use App\Models\LegacyStageType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStageTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStageType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->current(),
            'nm_tipo' => $this->faker->word,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->unique()->make(),
            'num_etapas' => $this->faker->numberBetween(1, 4),
            'descricao' => $this->faker->sentence,
            'num_meses' => $this->faker->numberBetween(1, 12),
            'num_semanas' => $this->faker->numberBetween(1, 52),
            'ativo' => 1,
        ];
    }

    public function unique()
    {
        return $this->state(function () {
            $stageType = LegacyStageType::query()->first();

            if (empty($stageType)) {
                $stageType = LegacyStageTypeFactory::new()->create();
            }

            return $stageType->getAttributes();
        });
    }
}
