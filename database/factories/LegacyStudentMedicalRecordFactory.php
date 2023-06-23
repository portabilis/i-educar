<?php

namespace Database\Factories;

use App\Models\LegacyStudentMedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentMedicalRecordFactory extends Factory
{
    public $model = LegacyStudentMedicalRecord::class;

    public function definition()
    {
        return [
            'ref_cod_aluno' => fn () => LegacyStudentFactory::new()->create(),
            'grupo_sanguineo' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'fator_rh' => $this->faker->randomElement(['+', '-']),
            'alergia_medicamento' => $this->faker->randomElement(['S', 'N']),
            'desc_alergia_medicamento' => $this->faker->words(7, true),
            'alergia_alimento' => $this->faker->randomElement(['S', 'N']),
            'desc_alergia_alimento' => $this->faker->words(7, true),
            'doenca_congenita' => $this->faker->randomElement(['S', 'N']),
            'desc_doenca_congenita' => $this->faker->words(7, true),
            'fumante' => $this->faker->randomElement(['S', 'N']),
            'doenca_caxumba' => $this->faker->randomElement(['S', 'N']),
            'doenca_sarampo' => $this->faker->randomElement(['S', 'N']),
            'doenca_rubeola' => $this->faker->randomElement(['S', 'N']),
            'doenca_catapora' => $this->faker->randomElement(['S', 'N']),
            'doenca_escarlatina' => $this->faker->randomElement(['S', 'N']),
            'doenca_coqueluche' => $this->faker->randomElement(['S', 'N']),
            'doenca_outras' => $this->faker->words(3, true),
            'epiletico' => $this->faker->randomElement(['S', 'N']),
            'epiletico_tratamento' => $this->faker->randomElement(['S', 'N']),
            'hemofilico' => $this->faker->randomElement(['S', 'N']),
            'hipertenso' => $this->faker->randomElement(['S', 'N']),
            'asmatico' => $this->faker->randomElement(['S', 'N']),
            'diabetico' => $this->faker->randomElement(['S', 'N']),
            'insulina' => $this->faker->randomElement(['S', 'N']),
            'tratamento_medico' => $this->faker->randomElement(['S', 'N']),
            'desc_tratamento_medico' => $this->faker->words(7, true),
            'medicacao_especifica' => $this->faker->randomElement(['S', 'N']),
            'desc_medicacao_especifica' => $this->faker->words(7, true),
            'acomp_medico_psicologico' => $this->faker->randomElement(['S', 'N']),
            'desc_acomp_medico_psicologico' => $this->faker->words(7, true),
            'restricao_atividade_fisica' => $this->faker->randomElement(['S', 'N']),
            'desc_restricao_atividade_fisica' => $this->faker->words(7, true),
            'fratura_trauma' => $this->faker->randomElement(['S', 'N']),
            'desc_fratura_trauma' => $this->faker->words(7, true),
            'plano_saude' => $this->faker->randomElement(['S', 'N']),
            'desc_plano_saude' => $this->faker->word,
            'responsavel' => $this->faker->name(),
            'responsavel_parentesco' => $this->faker->randomElement(['pai', 'mae', 'irmao', 'irma', 'tio', 'tia', 'avô', 'avó', 'outro']),
            'responsavel_parentesco_telefone' => '(99) 9999-9999',
            'responsavel_parentesco_celular' => '(99) 99999-9999',
            'observacao' => $this->faker->words(7, true),
            'aceita_hospital_proximo' => $this->faker->randomElement(['S', 'N']),
            'desc_aceita_hospital_proximo' => $this->faker->words(7, true),
        ];
    }
}
