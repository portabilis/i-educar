<?php

namespace App\Services;

class StageScoreCalculationService
{
    /**
     * Cálcula a média entre a nota da etapa e a nota da recuperação.
     *
     * @see StageScoreCalculationServiceTest::testCalculateAverageBetweenScoreAndRemedial()
     *
     * @param float      $score
     * @param float|null $remedial
     *
     * @return float
     */
    public function calculateAverageBetweenScoreAndRemedial($score, $remedial = null)
    {
        if (is_numeric($remedial)) {
            return (floatval($score) + floatval($remedial)) / 2;
        }

        return $score;
    }

    /**
     * Dobra a nota da etapa ou soma a nota de recuperação a ela.
     *
     * @see StageScoreCalculationServiceTest::testCalculateDoubleScore()
     *
     * @param float      $score
     * @param float|null $remedial
     *
     * @return float
     */
    public function calculateDoubleScore($score, $remedial = null)
    {
        if (is_numeric($remedial)) {
            return floatval($score) + floatval($remedial);
        }

        return $score * 2;
    }

    /**
     * Substitui a nota da etapa pela nota de recuperação caso ela seja maior.
     *
     * @see StageScoreCalculationServiceTest::testCalculateRemedial()
     *
     * @param float      $score
     * @param float|null $remedial
     *
     * @return float
     */
    public function calculateRemedial($score, $remedial = null)
    {
        if (is_numeric($remedial)) {
            return $remedial > $score ? $remedial : $score;
        }

        return $score;
    }
}
