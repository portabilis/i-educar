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
            // O início parte do fim: quem informa só o fim não fica com uma
            // etapa que termina antes de começar
            'data_fim' => now()->addMonths(3),
            'data_inicio' => fn (array $attributes) => Carbon::make($attributes['data_fim'])->subMonths(6),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
