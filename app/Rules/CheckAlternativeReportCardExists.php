<?php

namespace App\Rules;

use App\Models\LegacyGrade;
use Illuminate\Contracts\Validation\Rule;

class CheckAlternativeReportCardExists implements Rule
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
        $levelId = $value->ref_ref_cod_serie;
        $academicYear = $value->ano;
        $alternativeReportCard = $value->tipo_boletim_diferenciado;

        if ($alternativeReportCard || $value->multiseriada == 1) {
            return true;
        }

        $evaluationRule = LegacyGrade::findOrFail($levelId)->evaluationRules()
            ->wherePivot('ano_letivo', $academicYear)
            ->get()
            ->first();

        if (!empty($evaluationRule->regra_diferenciada_id)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo Boletim diferenciado é obrigatório quando a regra de avaliação da série possui regra diferenciada definida.';
    }
}
