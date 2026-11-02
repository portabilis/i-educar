<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    $this->evaluationRule = LegacyEvaluationRuleFactory::new()->withoutScore()->create();
});

/**
 * O aluno deve ser aprovado ao lançar todas as faltas
 */
test('post absence should returns approved', function () {
    $enrollment = $this->getCommonFakeData($this->evaluationRule);
    $schoolClass = $enrollment->schoolClass;
    $school = $schoolClass->school;
    $registration = $enrollment->registration;

    $this->createStages($school, 1);
    $this->createDisciplines($schoolClass, 1);

    $discipline = $schoolClass->refresh()->disciplines()->first();

    $response = $this->postAbsence($enrollment, $discipline->id, 1, 10);

    $this->assertEquals('Aprovado', $response->situacao);
    $this->assertEquals(App_Model_MatriculaSituacao::APROVADO, $registration->refresh()->aprovado);
});

/**
 * O aluno deve continuar cursando quando não forem lançadas as faltas de todas as etapas
 */
test('post a part of absence should returns studying', function () {
    $enrollment = $this->getCommonFakeData($this->evaluationRule);
    $schoolClass = $enrollment->schoolClass;
    $school = $schoolClass->school;
    $registration = $enrollment->registration;

    $this->createStages($school, 2);
    $this->createDisciplines($schoolClass, 1);

    $discipline = $schoolClass->refresh()->disciplines()->first();

    $response = $this->postAbsence($enrollment, $discipline->id, 1, 10);

    $this->assertEquals('Cursando', $response->situacao);
    $this->assertEquals(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
});
