<?php

namespace Tests\Unit\Services;

use App\Services\StageScoreCalculationService;
use Tests\TestCase;

class StageScoreCalculationServiceTest extends TestCase
{
    /**
     * @var StageScoreCalculationService
     */
    private $service;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StageScoreCalculationService();
    }

    /**
     * @return void
     */
    public function testCalculateAverageBetweenScoreAndRemedial()
    {
        // (5 + 9) / 2 = 7
        // 7 > 5 = 7
        $score1 = 5;
        $remedial1 = 9;
        $expected1 = 7;

        // Quando não existir a nota de recuperação, o valor da nota
        // substituirá a mesma no cálculo.

        // (6 + 6) / 2 = 6
        // 6 > 6 = 6
        $score2 = 6;
        $remedial2 = null;
        $expected2 = 6;

        // `null` significa que a recuperação não foi informada.
        // `0` significa que o a nota na recuperação foi zero.

        // (6 + 0) / 2 = 3
        // 6 > 3 = 6
        $score3 = 6;
        $remedial3 = 0;
        $expected3 = 6;

        $result1 = $this->service->calculateAverageBetweenScoreAndRemedial(
            $score1, $remedial1
        );

        $result2 = $this->service->calculateAverageBetweenScoreAndRemedial(
            $score2, $remedial2
        );

        $result3 = $this->service->calculateAverageBetweenScoreAndRemedial(
            $score3, $remedial3
        );

        $this->assertEquals($expected1, $result1);
        $this->assertEquals($expected2, $result2);
        $this->assertEquals($expected3, $result3);
    }

    /**
     * @return void
     */
    public function testCalculateSumScore()
    {
        // 5 + 9 = 14
        $score1 = 5;
        $remedial1 = 9;
        $expected1 = 14;

        // Quando não existir a nota de recuperação, o valor da nota
        // substituirá a mesma no cálculo.

        // 6 + 6 = 12
        $score2 = 6;
        $remedial2 = null;
        $expected2 = 12;

        // `null` significa que a recuperação não foi informada.
        // `0` significa que o a nota na recuperação foi zero.

        // 6 + 0 = 6
        $score3 = 6;
        $remedial3 = 0;
        $expected3 = 6;

        $result1 = $this->service->calculateSumScore(
            $score1, $remedial1
        );

        $result2 = $this->service->calculateSumScore(
            $score2, $remedial2
        );

        $result3 = $this->service->calculateSumScore(
            $score3, $remedial3
        );

        $this->assertEquals($expected1, $result1);
        $this->assertEquals($expected2, $result2);
        $this->assertEquals($expected3, $result3);
    }

    /**
     * @return void
     */
    public function testCalculateRemedial()
    {
        // 5 > 9 = 9
        $score1 = 5;
        $remedial1 = 9;
        $expected1 = 9;

        // Quando não existir a nota de recuperação, o numeral `0`
        // substituirá a mesma no cálculo.

        // 6 > 0 = 6
        $score2 = 6;
        $remedial2 = null;
        $expected2 = 6;

        // 6 > 0 = 6
        $score3 = 6;
        $remedial3 = 0;
        $expected3 = 6;

        $result1 = $this->service->calculateRemedial(
            $score1, $remedial1
        );

        $result2 = $this->service->calculateRemedial(
            $score2, $remedial2
        );

        $result3 = $this->service->calculateRemedial(
            $score3, $remedial3
        );

        $this->assertEquals($expected1, $result1);
        $this->assertEquals($expected2, $result2);
        $this->assertEquals($expected3, $result3);
    }
}
