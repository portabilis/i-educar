<?php

namespace Tests\Unit\Intranet;

use App\Models\LegacyEnrollment;
use App_Model_MatriculaSituacao;
use clsPmieducarMatriculaTurma;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * O que a classe legada de enturmação faz e as telas de matrícula e
 * enturmação não alcançam: a gravação com etapa, turma unificada e turno, a
 * edição de cada campo, os filtros das três listagens e as consultas de datas
 * e de sequência.
 */
class ClsPmieducarMatriculaTurmaTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private EnrollmentScenario $scenario;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();

        $this->user = $user->getKey();
        $this->scenario = EnrollmentScenario::for($user, 2030);
    }

    private function row(int $registration, int $schoolClass, int $sequence = 1): ?object
    {
        return DB::table('pmieducar.matricula_turma')
            ->where('ref_cod_matricula', $registration)
            ->where('ref_cod_turma', $schoolClass)
            ->where('sequencial', $sequence)
            ->first();
    }

    public function test_cadastra_com_etapa_turma_unificada_turno_e_data(): void
    {
        $registration = $this->scenario->registration()->getKey();
        $first = $this->scenario->schoolClass()->getKey();
        $second = $this->scenario->schoolClass()->getKey();

        $object = new clsPmieducarMatriculaTurma($registration, $first, null, $this->user, data_enturmacao: '2030-03-10');
        $object->etapa_educacenso = 2;
        $object->turma_unificada = 1;
        $object->turno_id = 2;

        $this->assertTrue($object->cadastra());

        $row = $this->row($registration, $first);

        $this->assertSame(2, $row->etapa_educacenso);
        $this->assertSame(1, $row->turma_unificada);
        $this->assertSame(2, $row->turno_id);
        $this->assertSame('2030-03-10', $row->data_enturmacao);
        $this->assertSame(1, $row->ativo);
        $this->assertSame($this->user, $row->ref_usuario_cad);

        // A numeração segue por matrícula, não por turma.
        $this->assertTrue((new clsPmieducarMatriculaTurma($registration, $second, null, $this->user, data_enturmacao: '2030-04-01'))->cadastra());
        $this->assertNotNull($this->row($registration, $second, 2));
    }

    public function test_edita_cada_campo(): void
    {
        $enrollment = $this->scenario->enrolled('Ana Alves', $this->scenario->schoolClass(), ['etapa_educacenso' => 1, 'turno_id' => 1]);
        $destination = $this->scenario->schoolClass()->getKey();

        $object = new clsPmieducarMatriculaTurma(
            $enrollment->ref_cod_matricula,
            $enrollment->ref_cod_turma,
            $this->user,
            $this->user,
            '2030-03-01 08:00:00',
            '2030-06-30',
            0,
            $destination,
            $enrollment->sequencial,
            '2030-03-05'
        );

        $object->etapa_educacenso = 2;
        $object->turma_unificada = 1;
        $object->turno_id = '3';
        $object->transferido = true;
        $object->remanejado = true;
        $object->reclassificado = true;
        $object->falecido = true;
        $object->abandono = true;
        $object->tipo_atendimento = '1,2';

        $this->assertTrue($object->edita());

        // A turma de transferência troca a turma da enturmação.
        $row = $this->row($enrollment->ref_cod_matricula, $destination, $enrollment->sequencial);

        $this->assertSame(0, $row->ativo);
        $this->assertSame(2, $row->etapa_educacenso);
        $this->assertSame(1, $row->turma_unificada);
        $this->assertSame(3, $row->turno_id);
        $this->assertSame('2030-03-05', $row->data_enturmacao);
        $this->assertStringStartsWith('2030-06-30', $row->data_exclusao);
        $this->assertStringStartsWith('2030-03-01 08:00:00', $row->data_cadastro);
        $this->assertSame('{1,2}', $row->tipo_atendimento);
        $this->assertSame($this->user, $row->ref_usuario_exc);
        $this->assertTrue($row->transferido);
        $this->assertTrue($row->remanejado);
        $this->assertTrue($row->reclassificado);
        $this->assertTrue($row->falecido);
        $this->assertTrue($row->abandono);
    }

    public function test_edita_limpando_etapa_turno_data_de_saida_e_tipo_de_atendimento(): void
    {
        $enrollment = $this->scenario->enrolled('Ana Alves', $this->scenario->schoolClass(), [
            'etapa_educacenso' => 1,
            'turno_id' => 1,
            'data_exclusao' => '2030-06-30',
            'tipo_atendimento' => '{1}',
        ]);

        $object = new clsPmieducarMatriculaTurma($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma, $this->user, ativo: 1, sequencial: $enrollment->sequencial);

        $object->etapa_educacenso = 0;
        $object->turno_id = '0';
        $object->transferido = true;
        $object->remanejado = true;
        $object->tipo_atendimento = null;

        $this->assertTrue($object->edita());

        $row = $this->row($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma, $enrollment->sequencial);

        $this->assertNull($row->etapa_educacenso);
        $this->assertNull($row->turno_id);
        $this->assertNull($row->data_exclusao);
        $this->assertNull($row->tipo_atendimento);
        // A enturmação ativa nunca fica marcada como transferida ou remanejada.
        $this->assertFalse($row->transferido);
        $this->assertFalse($row->remanejado);
    }

    public function test_nao_grava_nem_consulta_sem_os_codigos(): void
    {
        $empty = new clsPmieducarMatriculaTurma;

        $this->assertFalse($empty->cadastra());
        $this->assertFalse($empty->edita());
        $this->assertFalse($empty->detalhe());
        $this->assertFalse($empty->existe());
        $this->assertFalse($empty->excluir());
        $this->assertFalse($empty->buscaSequencialMax());
        $this->assertFalse($empty->getInstituicao());
        $this->assertFalse($empty->getDataBaseRemanejamento());
        $this->assertFalse($empty->getDataBaseTransferencia());
        $this->assertFalse($empty->reclassificacao());
        $this->assertFalse($empty->getDataSaidaEnturmacaoAnterior(null, null));
        $this->assertFalse($empty->getDataEntradaEnturmacaoSeguinte(null, null));
        $this->assertFalse($empty->getUltimaEnturmacao(null));
    }

    public function test_confirma_a_existencia_e_exclui_desativando(): void
    {
        $enrollment = $this->scenario->enrolled('Ana Alves', $this->scenario->schoolClass());

        $object = new clsPmieducarMatriculaTurma($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma, $this->user, sequencial: $enrollment->sequencial);

        $this->assertNotFalse($object->existe());
        $this->assertTrue($object->excluir());
        $this->assertSame(0, $enrollment->refresh()->ativo);
        $this->assertFalse((new clsPmieducarMatriculaTurma($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma, sequencial: 9))->existe());
    }

    public function test_informa_a_saida_da_enturmacao_anterior_e_a_entrada_da_seguinte(): void
    {
        $registration = $this->scenario->registration();

        $this->scenario->enroll($registration, $this->scenario->schoolClass(), [
            'sequencial' => 1,
            'ativo' => 0,
            'data_enturmacao' => '2030-02-05',
            'data_exclusao' => '2030-04-10',
        ]);

        $this->scenario->enroll($registration, $this->scenario->schoolClass(), ['sequencial' => 2, 'data_enturmacao' => '2030-04-11']);

        $object = new clsPmieducarMatriculaTurma;

        $this->assertSame('2030-04-10', $object->getDataSaidaEnturmacaoAnterior($registration->getKey(), 2));
        $this->assertSame('2030-04-11', $object->getDataEntradaEnturmacaoSeguinte($registration->getKey(), 1));
        $this->assertNull($object->getDataSaidaEnturmacaoAnterior($registration->getKey(), 1));
        $this->assertNull($object->getDataEntradaEnturmacaoSeguinte($registration->getKey(), 2));
    }

    public function test_marca_a_enturmacao_como_falecido_e_como_reclassificada(): void
    {
        $deceased = $this->scenario->enrolled('Ana Alves', $this->scenario->schoolClass());
        $reclassified = $this->scenario->enrolled('Bruno Barros', $this->scenario->schoolClass());

        (new clsPmieducarMatriculaTurma($deceased->ref_cod_matricula, $deceased->ref_cod_turma, sequencial: $deceased->sequencial))->marcaAlunoFalecido('15/05/2030');

        $this->assertTrue((new clsPmieducarMatriculaTurma($reclassified->ref_cod_matricula))->reclassificacao('2030-05-20'));

        $deceased->refresh();
        $reclassified->refresh();

        $this->assertTrue((bool) $deceased->falecido);
        $this->assertSame('2030-05-15', $deceased->data_exclusao->format('Y-m-d'));
        $this->assertTrue((bool) $reclassified->reclassificado);
        $this->assertSame(0, $reclassified->ativo);
        $this->assertSame('2030-05-20', $reclassified->data_exclusao->format('Y-m-d'));
    }

    /**
     * Duas enturmações que diferem em tudo o que a listagem filtra: a
     * primeira é a que cada filtro deve devolver sozinha.
     *
     * @return array{0: LegacyEnrollment, 1: LegacyEnrollment}
     */
    private function pair(): array
    {
        $second = LegacyUserFactory::new()->admin()->create();
        $other = EnrollmentScenario::for($second, 2029);

        $wanted = $this->scenario->enroll(
            $this->scenario->registration([
                'ref_cod_aluno' => $this->scenario->student('Ana Alves'),
                'aprovado' => App_Model_MatriculaSituacao::APROVADO,
                'semestre' => 2,
            ]),
            $this->scenario->schoolClass(['turma_turno_id' => $this->scenario->period(1), 'etapa_educacenso' => 3]),
            [
                'sequencial' => 1,
                'ref_usuario_cad' => $this->user,
                'ref_usuario_exc' => $this->user,
                'data_cadastro' => '2030-03-10 08:00:00',
                'data_exclusao' => '2030-04-10',
            ]
        );

        $unwanted = $other->enroll(
            $other->registration([
                'ref_cod_aluno' => $other->student('Bruno Barros'),
                'semestre' => 1,
                'ultima_matricula' => 0,
                'dependencia' => true,
            ]),
            $other->schoolClass(['turma_turno_id' => $other->period(2)]),
            [
                'sequencial' => 2,
                'ref_usuario_cad' => $second->getKey(),
                'ref_usuario_exc' => $second->getKey(),
                'data_cadastro' => '2030-06-10 08:00:00',
            ]
        );

        return [$wanted, $unwanted];
    }

    public function test_lista_por_cada_filtro(): void
    {
        [$wanted, $unwanted] = $this->pair();

        $registration = $wanted->registration;
        $both = [$wanted->ref_cod_matricula, $unwanted->ref_cod_matricula];

        $list = function (array $filters) use ($both) {
            $rows = (new clsPmieducarMatriculaTurma)->lista(...$filters) ?: [];

            return array_values(array_intersect(array_column($rows, 'ref_cod_matricula'), $both));
        };

        foreach ([
            ['int_ref_cod_matricula' => $wanted->ref_cod_matricula],
            ['int_ref_cod_turma' => $wanted->ref_cod_turma],
            ['int_ref_usuario_exc' => $this->user],
            ['int_ref_usuario_cad' => $this->user],
            ['date_data_cadastro_ini' => '2030-03-01', 'date_data_cadastro_fim' => '2030-04-01'],
            ['date_data_exclusao_ini' => '2030-04-01', 'date_data_exclusao_fim' => '2030-05-01'],
            ['int_ref_cod_serie' => $registration->ref_ref_cod_serie],
            ['int_ref_cod_serie' => $registration->ref_ref_cod_serie, 'int_ref_cod_serie_mult' => 0],
            ['int_ref_cod_curso' => $registration->ref_cod_curso],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ref_cod_instituicao' => $this->scenario->institution],
            ['int_ref_cod_aluno' => $registration->ref_cod_aluno],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ultima_matricula' => 1],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'apenasTurmasMultiSeriadas' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'apenasTurmasUnificadas' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'aprovado' => [App_Model_MatriculaSituacao::APROVADO, App_Model_MatriculaSituacao::REPROVADO]],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'aprovado' => App_Model_MatriculaSituacao::APROVADO],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'bool_aprovados_reprovados' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ano_matricula' => 2030],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_semestre' => 2],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'mes' => 3],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'mes_menor_que' => 5],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_sequencial' => 1],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_turma_turno_id' => 1],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ano_turma' => 2030],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'mes_matricula_inicial' => 5],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'diario' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'dependencia' => 'f'],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'ultimoSequencialNaTurma' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ativo' => 1, 'bool_matricula_ativo' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ativo' => 2],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ativo' => 3],
            // O ano letivo em andamento mais recente da escola é o da matrícula.
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'bool_escola_andamento' => true, 'pegar_ano_em_andamento' => true],
        ] as $filters) {
            $this->assertSame([$wanted->ref_cod_matricula], $list($filters), json_encode($filters));
        }

        // Enturmação ou matrícula inativas, e o ano corrente (que não é o da matrícula).
        foreach ([
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ativo' => 0],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'bool_matricula_ativo' => false],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'bool_escola_andamento' => true],
        ] as $filters) {
            $this->assertSame([], $list($filters), json_encode($filters));
        }
    }

    public function test_lista_com_o_nome_do_aluno_e_com_um_campo_so(): void
    {
        [$wanted] = $this->pair();

        $named = (new clsPmieducarMatriculaTurma)->lista(int_ref_cod_matricula: $wanted->ref_cod_matricula, bool_get_nome_aluno: true);

        $this->assertSame('Ana Alves', $named[0]['nome_aluno']);
        $this->assertSame(1, (int) $named[0]['_total']);

        $object = new clsPmieducarMatriculaTurma;
        $object->setCamposLista('ref_cod_turma');

        $this->assertEquals([$wanted->ref_cod_turma], $object->lista(int_ref_cod_matricula: $wanted->ref_cod_matricula));
    }

    public function test_lista3_por_cada_filtro(): void
    {
        [$wanted, $unwanted] = $this->pair();

        $registration = $wanted->registration;
        $both = [$wanted->ref_cod_matricula, $unwanted->ref_cod_matricula];

        $list = function (array $filters) use ($both) {
            $rows = (new clsPmieducarMatriculaTurma)->lista3(...$filters) ?: [];

            return array_values(array_intersect(array_column($rows, 'ref_cod_matricula'), $both));
        };

        foreach ([
            ['int_ref_cod_matricula' => $wanted->ref_cod_matricula],
            ['int_ref_cod_turma' => $wanted->ref_cod_turma],
            ['int_ref_usuario_exc' => $this->user],
            ['int_ref_usuario_cad' => $this->user],
            ['date_data_cadastro_ini' => '2030-03-01', 'date_data_cadastro_fim' => '2030-04-01'],
            ['date_data_exclusao_ini' => '2030-04-01', 'date_data_exclusao_fim' => '2030-05-01'],
            ['int_ref_cod_serie' => $registration->ref_ref_cod_serie],
            ['int_ref_cod_curso' => $registration->ref_cod_curso],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola],
            ['int_ref_cod_aluno' => $registration->ref_cod_aluno],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ultima_matricula' => 1],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'aprovado' => [App_Model_MatriculaSituacao::APROVADO]],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'aprovado' => App_Model_MatriculaSituacao::APROVADO],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ano_matricula' => 2030],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_sequencial' => 1],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_matricula_ativo' => true],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_semestre' => 2],
        ] as $filters) {
            $this->assertSame([$wanted->ref_cod_matricula], $list($filters), json_encode($filters));
        }

        foreach ([
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_ativo' => 0],
            ['int_ref_cod_escola' => $registration->ref_ref_cod_escola, 'int_matricula_ativo' => false],
        ] as $filters) {
            $this->assertSame([], $list($filters), json_encode($filters));
        }

        $object = new clsPmieducarMatriculaTurma;
        $object->setCamposLista('ref_cod_turma');

        $this->assertEquals([$wanted->ref_cod_turma], $object->lista3(int_ref_cod_matricula: $wanted->ref_cod_matricula));
    }

    public function test_lista4_devolve_as_enturmacoes_ativas_da_serie_com_um_campo_so(): void
    {
        [$wanted] = $this->pair();

        $registration = $wanted->registration;

        $object = new clsPmieducarMatriculaTurma;
        $object->setCamposLista('ref_cod_matricula');

        $this->assertEquals(
            [$wanted->ref_cod_matricula],
            $object->lista4($registration->ref_ref_cod_escola, $registration->ref_cod_curso, $registration->ref_ref_cod_serie, $wanted->ref_cod_turma, 2030)
        );

        $this->assertFalse($object->lista4($registration->ref_ref_cod_escola, ano: 2030, saida_escola: true));
    }
}
