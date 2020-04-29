<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App_Model_IedFinder;

class GlobalAverageService
{
    public function getGlobalAverage(LegacyRegistration $registration)
    {
        $evaluationRule = App_Model_IedFinder::getRegraAvaliacaoPorMatricula($registration->getKey());

        if ($this->isGlobalScore($evaluationRule)) {
            return  $this->getAverageGlobalScore($registration);
        }

        return $this->getAverage($registration);
    }

    private function isGlobalScore(\RegraAvaliacao_Model_Regra $evaluationRule)
    {
        return $evaluationRule->notaGeralPorEtapa == 1;
    }

    private function getAverageGlobalScore(LegacyRegistration $registration)
    {
        if (
            empty($registration->studentScore)
            || empty($registration->studentScore->averageGeneral)
        ) {
            return 0;
        }

        $scores = $registration->studentScore->averageGeneral->pluck('media');

        return $scores->avg();
    }

    private function getAverage(LegacyRegistration $registration)
    {
        if (
            empty($registration->studentScore)
            || empty($registration->studentScore->averageByDiscipline)
        ) {
            return 0;
        }

        $scores = $registration->studentScore->averageByDiscipline->pluck('media');

        return $scores->avg();
    }
}
