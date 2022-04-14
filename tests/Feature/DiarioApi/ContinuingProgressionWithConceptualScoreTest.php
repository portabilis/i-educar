<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRoundingTableFactory;
use Database\Factories\LegacyValueRoundingTableFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContinuingProgressionWithConceptualScoreTest extends TestCase
{
    use DiarioApiFakeDataTestTrait;
    use DiarioApiRequestTestTrait;
    use DatabaseTransactions;

    /**
     * @var LegacyEnrollment
     */
    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getContinuingProgressionWithConceptualScoreTest();
    }

    /**
     * Cria dados base para testes das regras de avaliação não continuada
     *
     * @return LegacyEnrollment
     */
    public function getContinuingProgressionWithConceptualScoreTest()
    {
        $roundingTable = LegacyRoundingTableFactory::new()->conceitual()->create();

        $valuesRoundingTable = [
            [
                'nome' => 'E',
                'descricao' => 'Insuficiente',
                'valor_minimo' => 0,
                'valor_maximo' => 1.9
            ],
            [
                'nome' => 'D',
                'descricao' => 'Irregular',
                'valor_minimo' => 2,
                'valor_maximo' => 3.9
            ],
            [
                'nome' => 'C',
                'descricao' => 'Bom',
                'valor_minimo' => 4,
                'valor_maximo' => 5.9
            ],
            [
                'nome' => 'B',
                'descricao' => 'Muito Bom',
                'valor_minimo' => 6,
                'valor_maximo' => 7.9
            ],
            [
                'nome' => 'A',
                'descricao' => 'Excelente',
                'valor_minimo' => 8,
                'valor_maximo' => 10
            ],
        ];

        foreach ($valuesRoundingTable as $value) {
            LegacyValueRoundingTableFactory::new()->create([
                'tabela_arredondamento_id' => $roundingTable->id,
                'nome' => $value['nome'],
                'descricao' => $value['descricao'],
                'valor_minimo' => $value['valor_minimo'],
                'valor_maximo' => $value['valor_maximo'],
            ]);
        }

        $evaluationRule = LegacyEvaluationRuleFactory::new()->progressaoContinuadaNotaConceitual()->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);
        $schoolClass = $enrollment->schoolClass;
        $school = $schoolClass->school;
        $this->createStages($school, 4);
        $this->createDisciplines($schoolClass, 2);

        return $enrollment;
    }

    public function testContinuingProgressionAfterAllScoreAndAbsencePosted()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 10,
            2 => 10,
            3 => 10,
            4 => 10,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    public function testContinuingProgressionAfterAllInsufficientScoreAndAbsencePosted()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 1.9,
            2 => 1.9,
            3 => 1.9,
            4 => 1.9,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    /**
     * Regra com progressão continuada
     *
     * Após o lançamentos das notas, não deverá reprovar mesmo com número de presenças insuficiente
     */
    public function testContinuingProgressionMoreThanAbsenceLimitAfterAllScoreAndAbsencePosted()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 8,
            2 => 8,
            3 => 8,
            4 => 8,
        ];

        $absence = [
            1 => 40,
            2 => 40,
            3 => 40,
            4 => 40,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    public function testContinuingProgressionAfterScoreAndAbsenceStagesPosted()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 1.9,
            2 => 1.9,
        ];

        $absence = [
            1 => 3,
            2 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Cursando', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testContinuingProgressionAfterAbsenceRemoveInLastStagesReturnsToStudying()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 10,
            2 => 10,
            3 => 10,
            4 => 10,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 4);
        $this->assertEquals('Cursando', $response->situacao);

        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testContinuingProgressionAfterRemoveAbsenceWhenNotIsLastStagesReturnsToStudying()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $disciplines = $schoolClass->disciplines;

        $score = [
            1 => 10,
            2 => 10,
            3 => 10,
            4 => 10,
        ];

        $absence = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
        ];

        foreach ($disciplines as $discipline) {
            $this->postAbsenceForStages($absence, $discipline);
            $response = $this->postScoreForStages($score, $discipline);

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 3);
        $this->assertTrue($response->any_error_msg);

        $this->assertEquals(1, $registration->refresh()->aprovado);
    }
}
