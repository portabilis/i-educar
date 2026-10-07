<?php

namespace Database\Factories;

use App\Models\LegacyGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGradeFactory extends Factory
{
    protected $model = LegacyGrade::class;

    public function definition(): array
    {
        return [
            'nm_serie' => $this->faker->words(3, true),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_curso' => fn () => LegacyCourseFactory::new()->create(),
            'etapa_curso' => 1,
            'carga_horaria' => 800,
            'data_cadastro' => now(),
            'idade_inicial' => 6,
            'idade_final' => 7,
            'descricao' => $this->faker->word(),
            // A tela só conhece 1 (não) e 2 (sim).
            'concluinte' => 1,
            'ativo' => 1,
            'importar_serie_pre_matricula' => false,
            'dias_letivos' => 200,
        ];
    }

    public function withEvaluationRule(): static
    {
        return $this->afterCreating(function (LegacyGrade $grade) {
            LegacyEvaluationRuleGradeYearFactory::new()->create([
                'regra_avaliacao_id' => LegacyEvaluationRuleFactory::new()->current(),
                'regra_avaliacao_diferenciada_id' => LegacyEvaluationRuleFactory::new()->current(),
                'serie_id' => $grade,
            ]);
        });
    }
}
