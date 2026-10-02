<?php

namespace Tests\Unit\Intranet;

use clsPmieducarSerie;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que a classe legada de série faz e as telas de série não alcançam: as
 * regras de avaliação gravadas na própria série, os filtros da listagem sem
 * campo na tela, a listagem de séries com componentes e o corte etário.
 */
class ClsPmieducarSerieTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private int $institution;

    private int $course;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();

        $this->user = $user->getKey();
        $this->institution = $user->ref_cod_instituicao;
        $this->course = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();
    }

    private function row(int $id): object
    {
        return DB::table('pmieducar.serie')->where('cod_serie', $id)->first();
    }

    public function test_cadastra_com_as_regras_de_avaliacao_da_serie(): void
    {
        $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();
        $differentiated = LegacyEvaluationRuleFactory::new()->create()->getKey();

        $id = (new clsPmieducarSerie(
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            ref_cod_curso: $this->course,
            nm_serie: 'Série Alfa',
            etapa_curso: 1,
            concluinte: 1,
            carga_horaria: 800,
            data_cadastro: '2026-01-10',
            data_exclusao: '2026-01-11',
            ativo: 1,
            regra_avaliacao_id: $rule,
            dias_letivos: 200,
            regra_avaliacao_diferenciada_id: $differentiated,
        ))->cadastra();

        $row = $this->row($id);

        $this->assertSame($rule, $row->regra_avaliacao_id);
        $this->assertSame($differentiated, $row->regra_avaliacao_diferenciada_id);
        $this->assertSame('Série Alfa', $row->nm_serie);
    }

    public function test_estoura_ao_receber_uma_regra_de_avaliacao_que_nao_existe(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nenhum registro encontrado');

        new clsPmieducarSerie(regra_avaliacao_id: 999999999);
    }

    public function test_estoura_ao_receber_uma_regra_diferenciada_que_nao_existe(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nenhum registro encontrado');

        new clsPmieducarSerie(regra_avaliacao_diferenciada_id: 999999999);
    }

    public function test_nao_cadastra_sem_os_campos_obrigatorios(): void
    {
        $this->assertFalse((new clsPmieducarSerie(nm_serie: 'Série incompleta'))->cadastra());
        $this->assertDatabaseMissing('pmieducar.serie', ['nm_serie' => 'Série incompleta']);
    }

    public function test_edita_os_campos_que_o_formulario_nao_envia_e_as_opcoes_ligadas(): void
    {
        $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();
        $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course])->getKey();

        $serie = new clsPmieducarSerie(
            cod_serie: $id,
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            data_cadastro: '2026-01-10',
            regra_avaliacao_id: $rule,
            regra_avaliacao_diferenciada_id: $rule,
            alerta_faixa_etaria: true,
            bloquear_matricula_faixa_etaria: true,
            exigir_inep: true,
            importar_serie_pre_matricula: true,
            etapa_educacenso: 14,
        );

        $this->assertTrue($serie->edita());

        $row = $this->row($id);

        $this->assertSame($this->user, $row->ref_usuario_cad);
        $this->assertSame('2026-01-10 00:00:00', $row->data_cadastro);
        $this->assertSame($rule, $row->regra_avaliacao_id);
        $this->assertSame($rule, $row->regra_avaliacao_diferenciada_id);
        $this->assertTrue($row->alerta_faixa_etaria);
        $this->assertTrue($row->bloquear_matricula_faixa_etaria);
        $this->assertTrue($row->exigir_inep);
        $this->assertTrue($row->importar_serie_pre_matricula);
        $this->assertSame(14, $row->etapa_educacenso);
    }

    public function test_nao_edita_nem_exclui_sem_o_codigo_e_o_usuario(): void
    {
        $this->assertFalse((new clsPmieducarSerie)->edita());
        $this->assertFalse((new clsPmieducarSerie)->excluir());
        $this->assertFalse((new clsPmieducarSerie)->detalhe());
        $this->assertFalse((new clsPmieducarSerie)->existe());
    }

    public function test_diz_se_a_serie_existe_e_se_tem_turmas(): void
    {
        $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course])->getKey();
        $withClass = LegacySchoolClassFactory::new()->create()->ref_ref_cod_serie;

        $this->assertNotEmpty((new clsPmieducarSerie($id))->existe());
        $this->assertEmpty((new clsPmieducarSerie(0))->existe());
        $this->assertFalse((bool) (new clsPmieducarSerie($id))->possuiTurmasVinculadas());
        $this->assertTrue((bool) (new clsPmieducarSerie($withClass))->possuiTurmasVinculadas());
    }

    public function test_lista_por_cada_filtro(): void
    {
        $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $this->course,
            'ref_usuario_cad' => $this->user,
            'ref_usuario_exc' => $this->user,
            'nm_serie' => 'Série Zeta',
            'etapa_curso' => 3,
            'concluinte' => 2,
            'carga_horaria' => 1234,
            'idade_inicial' => 11,
            'idade_final' => 12,
            'idade_ideal' => 11,
            'regra_avaliacao_id' => $rule,
            'data_cadastro' => '2020-06-15',
            'data_exclusao' => '2021-06-15',
        ]);
        $id = $grade->getKey();

        $schoolGrade = LegacySchoolGradeFactory::new()->create(['ref_cod_serie' => $grade, 'anos_letivos' => '{2030}']);

        $filters = [
            ['int_cod_serie' => $id],
            ['int_ref_usuario_exc' => $this->user, 'int_ref_usuario_cad' => $this->user],
            ['int_ref_cod_curso' => $this->course, 'str_nm_serie' => 'série zeta'],
            ['int_etapa_curso' => 3, 'int_concluinte' => 2, 'int_carga_horaria' => 1234],
            ['date_data_cadastro_ini' => '2020-06-01', 'date_data_cadastro_fim' => '2020-06-30'],
            ['date_data_exclusao_ini' => '2021-06-01', 'date_data_exclusao_fim' => '2021-06-30'],
            ['int_ref_cod_instituicao' => $this->institution, 'regra_avaliacao_id' => $rule],
            ['int_idade_inicial' => 11, 'int_idade_final' => 12, 'int_idade_ideal' => 11],
            ['int_ref_cod_escola' => $schoolGrade->ref_cod_escola],
            ['int_ref_cod_escola' => $schoolGrade->ref_cod_escola, 'ano' => 2030],
            ['int_cod_serie' => $id, 'ano' => 2030],
        ];

        foreach ($filters as $filter) {
            $lista = (new clsPmieducarSerie)->lista(...$filter);

            $this->assertContains($id, array_column($lista, 'cod_serie'), 'a série não veio com ' . json_encode($filter));
        }

        $this->assertSame([], (new clsPmieducarSerie)->lista(int_cod_serie: $id, ano: 1999));
        $this->assertSame([], (new clsPmieducarSerie)->lista(int_ref_cod_escola: $schoolGrade->ref_cod_escola, ano: 1999));
    }

    public function test_lista_as_inativas_e_um_campo_so(): void
    {
        $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Série Zeta', 'ativo' => 0])->getKey();

        $this->assertContains($id, array_column((new clsPmieducarSerie)->lista(int_cod_serie: $id, int_ativo: 0), 'cod_serie'));
        $this->assertSame([], (new clsPmieducarSerie)->lista(int_cod_serie: $id));

        $serie = new clsPmieducarSerie;
        $serie->setCamposLista('nm_serie');

        $this->assertSame(['Série Zeta'], $serie->lista(int_cod_serie: $id, int_ativo: 0));
    }

    public function test_lista_as_series_que_tem_componentes_vinculados(): void
    {
        $with = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course]);
        $without = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course]);
        LegacyDisciplineAcademicYearFactory::new()->create(['ano_escolar_id' => $with->getKey()]);

        $lista = (new clsPmieducarSerie)->listaSeriesComComponentesVinculados(
            int_cod_serie: $with->getKey(),
            int_ref_cod_curso: $this->course,
            int_ref_cod_instituicao: $this->institution,
            int_ativo: 1,
        );

        $this->assertSame([$with->getKey()], array_column($lista, 'cod_serie'));
        $this->assertSame($this->institution, $lista[0]['ref_cod_instituicao']);
        $this->assertNotContains($without->getKey(), array_column((new clsPmieducarSerie)->listaSeriesComComponentesVinculados(int_ref_cod_curso: $this->course), 'cod_serie'));

        $serie = new clsPmieducarSerie;
        $serie->setCamposLista('cod_serie');

        $this->assertSame([$with->getKey()], $serie->listaSeriesComComponentesVinculados(int_ref_cod_curso: $this->course));
    }

    public function test_detalhe_pode_restringir_a_serie_ao_curso(): void
    {
        $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Série Zeta'])->getKey();
        $otherCourse = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();

        $this->assertSame('Série Zeta', (new clsPmieducarSerie(cod_serie: $id, ref_cod_curso: $this->course))->detalhe()['nm_serie']);
        $this->assertEmpty((new clsPmieducarSerie(cod_serie: $id, ref_cod_curso: $otherCourse))->detalhe());
    }

    public function test_confere_o_corte_etario_pela_data_base_da_instituicao(): void
    {
        DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->institution)->update(['data_base_matricula' => '1990-03-31']);
        cache()->flush();

        $serie = new clsPmieducarSerie(
            LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'idade_inicial' => 6, 'idade_final' => 7])->getKey()
        );

        // Em 31/03/2026: quem nasceu em 2020 tem 6 anos (ou 5, depois de março); em 2015, tem 10.
        $this->assertTrue($serie->verificaPeriodoCorteEtarioDataNascimento('2020-01-15', 2026));
        $this->assertTrue($serie->verificaPeriodoCorteEtarioDataNascimento('2019-01-15', 2026));
        $this->assertFalse($serie->verificaPeriodoCorteEtarioDataNascimento('2020-06-15', 2026));
        $this->assertFalse($serie->verificaPeriodoCorteEtarioDataNascimento('2015-06-15', 2026));
    }

    public function test_sem_data_base_de_matricula_qualquer_idade_serve(): void
    {
        DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->institution)->update(['data_base_matricula' => null]);
        cache()->flush();

        $serie = new clsPmieducarSerie(
            LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'idade_inicial' => 6, 'idade_final' => 7])->getKey()
        );

        $this->assertTrue($serie->verificaPeriodoCorteEtarioDataNascimento('1980-06-15', 2026));
    }
}
