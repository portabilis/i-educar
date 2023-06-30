<?php

namespace Database\Factories;

use App\Models\LegacyStudentDescriptiveOpinion;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_TipoParecerDescritivo;

class LegacyStudentDescriptiveOpinionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudentDescriptiveOpinion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'matricula_id' => fn () => LegacyRegistrationFactory::new()->create(),
            'parecer_descritivo' => $this->faker->randomElement([
                RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
                RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            ]),
        ];
    }
}
