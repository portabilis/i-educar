<?php

use App\Models\LegacyEnrollment;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRoundingTableFactory;
use Database\Factories\LegacyValueRoundingTableFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    /**
     * Cria dados base para testes das regras de avaliação não continuada
     *
     * @return LegacyEnrollment
     */
    $this->getContinuingProgressionWithConceptualScoreTest = function () {
        $roundingTable = LegacyRoundingTableFactory::new()->conceitual()->create();

        $valuesRoundingTable = [
            [
                'nome' => 'E',
                'descricao' => 'Insuficiente',
                'valor_minimo' => 0,
                'valor_maximo' => 1.9,
            ],
            [
                'nome' => 'D',
                'descricao' => 'Irregular',
                'valor_minimo' => 2,
                'valor_maximo' => 3.9,
            ],
            [
                'nome' => 'C',
                'descricao' => 'Bom',
                'valor_minimo' => 4,
                'valor_maximo' => 5.9,
            ],
            [
                'nome' => 'B',
                'descricao' => 'Muito Bom',
                'valor_minimo' => 6,
                'valor_maximo' => 7.9,
            ],
            [
                'nome' => 'A',
                'descricao' => 'Excelente',
                'valor_minimo' => 8,
                'valor_maximo' => 10,
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
    };

    $this->enrollment = ($this->getContinuingProgressionWithConceptualScoreTest)();
});

test('continuing progression after all score and absence posted', function () {
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
});

test('continuing progression after all insufficient score and absence posted', function () {
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
});

/**
 * Regra com progressão continuada
 *
 * Após o lançamentos das notas, não deverá reprovar mesmo com número de presenças insuficiente
 */
test('continuing progression more than absence limit after all score and absence posted', function () {
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
});

test('continuing progression after score and absence stages posted', function () {
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
});

test('continuing progression after absence remove in last stages returns to studying', function () {
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
});

test('continuing progression after remove absence when not is last stages returns to studying', function () {
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
});
