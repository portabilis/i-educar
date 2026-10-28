<?php

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('busca matricula por aluno', function () {
    $enrollment = LegacyEnrollmentFactory::new()->create();

    $data = [
        'oper' => 'get',
        'resource' => 'matriculas',
        'aluno_id' => $enrollment->getStudentId(),
    ];

    $response = $this->getResource('/module/Api/Aluno', $data);
    $response->assertJsonStructure(
        [
            'any_error_msg',
            'matriculas' => [
                [
                    'aluno_id',
                    'aluno_nome',
                    'ano',
                    'curso_id',
                    'curso_nome',
                    'data_entrada',
                    'data_saida',
                    'escola_id',
                    'escola_nome',
                    'id',
                    'instituicao_id',
                    'serie_id',
                    'serie_nome',
                    'situacao',
                    'transferencia_em_aberto',
                    'turma_id',
                    'turma_nome',
                    'ultima_enturmacao',
                    'user_can_access',
                ],
            ],
            'msgs',
            'oper',
            'resource',
        ]
    );
});

test('retorna situacao cursando', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()
        ->withEnrollment($schoolClass)
        ->create([
            'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        ]);

    $response = $this->getResource('/module/Api/Matricula', [
        'oper' => 'get',
        'resource' => 'matriculas',
        'aluno_id' => $registration->ref_cod_aluno,
        'escola_id' => $schoolClass->school_id,
    ]);

    $response->assertJsonPath(
        'matriculas.0.situacao',
        App_Model_MatriculaSituacao::getInstance()->getValue(App_Model_MatriculaSituacao::EM_ANDAMENTO)
    );
});

test('retorna situacao aprovado', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $registration = LegacyRegistrationFactory::new()
        ->withEnrollment($schoolClass)
        ->create([
            'aprovado' => App_Model_MatriculaSituacao::APROVADO,
        ]);

    $response = $this->getResource('/module/Api/Matricula', [
        'oper' => 'get',
        'resource' => 'matriculas',
        'aluno_id' => $registration->ref_cod_aluno,
        'escola_id' => $schoolClass->school_id,
    ]);

    $response->assertJsonPath(
        'matriculas.0.situacao',
        App_Model_MatriculaSituacao::getInstance()
            ->getValue(App_Model_MatriculaSituacao::APROVADO)
    );
});

test('retorna todas situacoes permitidas', function () {
    $situacoes = [
        App_Model_MatriculaSituacao::REPROVADO,
        App_Model_MatriculaSituacao::EM_EXAME,
        App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
    ];

    foreach ($situacoes as $situacao) {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()
            ->withEnrollment($schoolClass)
            ->create([
                'aprovado' => $situacao,
            ]);

        $response = $this->getResource('/module/Api/Matricula', [
            'oper' => 'get',
            'resource' => 'matriculas',
            'aluno_id' => $registration->ref_cod_aluno,
            'escola_id' => $schoolClass->school_id,
        ]);

        $response->assertJsonPath(
            'matriculas.0.situacao',
            App_Model_MatriculaSituacao::getInstance()->getValue($situacao)
        );
    }
});
