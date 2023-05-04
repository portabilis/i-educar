<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineScoreAverage;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineScoreAverageFactory extends Factory
{
    protected $model = LegacyDisciplineScoreAverage::class;

    public function definition(): array
    {
        return [
            'nota_aluno_id' => fn () => LegacyRegistrationScoreFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'media' => $this->faker->randomFloat(1, 0, 10),
            'media_arredondada' => $this->faker->randomFloat(1, 0, 10),
            'etapa' => 1,
            'situacao' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
            'bloqueada' => false,
        ];
    }
}
