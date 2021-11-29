<?php

namespace App\Rules;

use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;
use RegraAvaliacao_Model_TipoRecuperacaoParalela;

class IncompatibleRetakeType implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return false;
        }

        $schoolClass = $value[0]['turma_id'];
        $schoolClass = LegacySchoolClass::find($schoolClass);
        $grades = array_column($value, 'serie_id');

        $retakeType = LegacyEvaluationRuleGradeYear::query()
            ->whereIn('serie_id', $grades)
            ->where('ano_letivo', $schoolClass->ano)
            ->with('evaluationRule')
            ->get()
            ->map(function ($model) {
                return [
                    'formula_recuperacao_id' => $model->evaluationRule->formula_recuperacao_id,
                    'tipo_recuperacao_paralela' => $model->evaluationRule->tipo_recuperacao_paralela,
                ];
            })
            ->toArray();

        $retakeFormula = array_filter(array_column($retakeType, 'formula_recuperacao_id'));
        $parallelRetake = array_column($retakeType, 'tipo_recuperacao_paralela');

        // Ignora regra que não usa recuperação
        $parallelRetake = array_diff($parallelRetake, [RegraAvaliacao_Model_TipoRecuperacaoParalela::NAO_USAR]);

        return count(array_unique($parallelRetake)) <= 1 && count(array_unique($retakeFormula)) <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'As séries selecionadas devem possuir o mesmo tipo de recuperação.';
    }
}
