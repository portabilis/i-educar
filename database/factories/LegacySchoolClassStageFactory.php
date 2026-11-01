<?php

namespace Database\Factories;

use App\Models\LegacySchoolClassStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LegacySchoolClassStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClassStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_cod_turma' => fn () => LegacySchoolClassFactory::new()->create(),
            'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
            'sequencial' => $this->faker->numberBetween(1, 9),
            // A etapa está em andamento e não sai do ano corrente: de outubro a
            // dezembro o fim seria no ano seguinte, e de janeiro a março o
            // início seria no anterior.
            'data_inicio' => now()->subMonths(3)->max(now()->startOfYear()),
            'data_fim' => fn (array $attributes) => now()->addMonths(3)->min(now()->endOfYear())->max(Carbon::make($attributes['data_inicio'])),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
