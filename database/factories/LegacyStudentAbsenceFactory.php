<?php

namespace Database\Factories;

use App\Models\LegacyStudentAbsence;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_TipoPresenca;

class LegacyStudentAbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudentAbsence::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'matricula_id' => fn () => LegacyRegistrationFactory::new()->create(),
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        ];
    }

    public function discipline(): static
    {
        return $this->state([
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);
    }
}
