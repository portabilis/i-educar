<?php

namespace App\Rules;

use App\Models\LegacySchoolClass;
use App\Services\iDiarioService;
use Illuminate\Contracts\Validation\Rule;

class CheckGradesAndAbsencesInStageIDiarioExists implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string            $attribute
     * @param LegacySchoolClass $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $turmaId = $value['schoolClass']->cod_turma;
        $anoTurma = $value['schoolClass']->ano;
        $etapasCount = count($value['startDates']);
        $etapasCountAntigo = $value['schoolClass']->stages()->count();

        if ($etapasCount < $etapasCountAntigo) {
            $etapasTmp = $etapasCount;
            $etapas = [];

            while ($etapasTmp < $etapasCountAntigo) {
                $etapasTmp += 1;
                $etapas[] = $etapasTmp;
            }

            $checkReleases = config('legacy.config.url_novo_educacao')
                && config('legacy.config.token_novo_educacao');

            if ($checkReleases) {
                $iDiarioService = app(iDiarioService::class);

                foreach ($etapas as $etapa) {
                    if ($iDiarioService->getStepActivityByClassroom($turmaId, $anoTurma, $etapa)) {
                        return false;
                    }
                }
            }
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
        return 'Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.';
    }
}
