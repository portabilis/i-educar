<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App_Model_MatriculaSituacao;

class DiarioRecuperaMatriculaAlunoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function test_busca_matricula_por_aluno()
    {
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
    }

    public function test_retorna_situacao_cursando()
    {
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
    }


public function test_retorna_situacao_aprovado()
{
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
}
public function test_retorna_todas_situacoes_permitidas()
{
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
}

}