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
     */
    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_tipo' => $this->faker->word,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'num_etapas' => $this->faker->numberBetween(1, 4),
            'descricao' => $this->faker->sentence,
            'num_meses' => $this->faker->numberBetween(1, 12),
            'num_semanas' => $this->faker->numberBetween(1, 52),
            'ativo' => 1,
        ];
    }

    public function semester(): LegacyStageType
    {
        $data = [
            'nm_tipo' => 'Semestre',
            'num_etapas' => 2,
        ];

        return LegacyStageType::query()->where($data)->first() ?? $this->create($data);
    }

    public function bimonthly(): LegacyStageType
    {
        $data = [
            'nm_tipo' => 'Bimestral',
            'num_etapas' => 4,
        ];

        return LegacyStageType::query()->where($data)->first() ?? $this->create($data);
    }

    public function quarterly(): LegacyStageType
    {
        $data = [
            'nm_tipo' => 'Trimestral',
            'num_etapas' => 3,
        ];

        return LegacyStageType::query()->where($data)->first() ?? $this->create($data);
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
