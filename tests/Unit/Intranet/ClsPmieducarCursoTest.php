<?php

namespace Tests\Unit\Intranet;

use clsPmieducarCurso;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que a classe legada de curso faz e as telas de curso não alcançam: os
 * filtros da listagem que não têm campo na tela, as consultas de apoio e os
 * campos que a edição aceita mas o formulário não envia.
 */
class ClsPmieducarCursoTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private int $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();

        $this->user = $user->getKey();
        $this->institution = $user->ref_cod_instituicao;
    }

    public function test_edita_os_campos_que_o_formulario_nao_envia(): void
    {
        $id = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();

        $curso = new clsPmieducarCurso(
            cod_curso: $id,
            ref_usuario_cad: $this->user,
            ato_poder_publico: 'Portaria 1',
            objetivo_curso: 'Formar leitores',
            publico_alvo: 'Crianças',
            data_cadastro: '2026-01-10',
            data_exclusao: '2026-01-11',
            ativo: 1,
            ref_usuario_exc: $this->user,
        );

        $this->assertTrue($curso->edita());

        $row = DB::table('pmieducar.curso')->where('cod_curso', $id)->first();

        $this->assertSame('Portaria 1', $row->ato_poder_publico);
        $this->assertSame('Formar leitores', $row->objetivo_curso);
        $this->assertSame('Crianças', $row->publico_alvo);
        $this->assertSame($this->user, $row->ref_usuario_cad);
        $this->assertSame('2026-01-10 00:00:00', $row->data_cadastro);
    }

    public function test_nao_edita_nem_exclui_sem_o_codigo_e_o_usuario(): void
    {
        $this->assertFalse((new clsPmieducarCurso)->edita());
        $this->assertFalse((new clsPmieducarCurso)->excluir());
        $this->assertFalse((new clsPmieducarCurso(cod_curso: 1))->excluir());
    }

    public function test_lista_por_cada_filtro(): void
    {
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create([
            'ref_cod_instituicao' => $this->institution,
            'ref_usuario_cad' => $this->user,
            'ref_usuario_exc' => $this->user,
            'nm_curso' => 'Curso Zeta',
            'sgl_curso' => 'CZETA',
            'qtd_etapas' => 7,
            'carga_horaria' => 1234,
            'hora_falta' => 0.5,
            'ato_poder_publico' => 'Portaria Zeta',
            'objetivo_curso' => 'Objetivo Zeta',
            'publico_alvo' => 'Público Zeta',
            'data_cadastro' => '2020-06-15',
            'data_exclusao' => '2021-06-15',
        ]);
        $id = $course->getKey();

        $filters = [
            ['int_cod_curso' => $id],
            ['int_ref_usuario_cad' => $this->user, 'int_ref_usuario_exc' => $this->user],
            ['int_ref_cod_tipo_regime' => $course->ref_cod_tipo_regime],
            ['int_ref_cod_nivel_ensino' => $course->ref_cod_nivel_ensino],
            ['int_ref_cod_tipo_ensino' => $course->ref_cod_tipo_ensino],
            ['str_nm_curso' => 'curso zeta'],
            ['str_sgl_curso' => 'CZETA'],
            ['int_qtd_etapas' => 7],
            ['int_carga_horaria' => 1234],
            ['str_ato_poder_publico' => 'Portaria Zeta'],
            ['str_objetivo_curso' => 'Objetivo Zeta'],
            ['str_publico_alvo' => 'Público Zeta'],
            ['date_data_cadastro_ini' => '2020-06-01', 'date_data_cadastro_fim' => '2020-06-30'],
            ['date_data_exclusao_ini' => '2021-06-01', 'date_data_exclusao_fim' => '2021-06-30'],
            ['int_ref_cod_instituicao' => $this->institution, 'int_padrao_ano_escolar' => 1],
            ['int_hora_falta' => 0.5],
        ];

        foreach ($filters as $filter) {
            $lista = (new clsPmieducarCurso)->lista(...$filter);

            $this->assertIsArray($lista, 'nada encontrado com ' . json_encode($filter));
            $this->assertContains($id, array_column($lista, 'cod_curso'), 'o curso não veio com ' . json_encode($filter));
        }

        $this->assertFalse((new clsPmieducarCurso)->lista(str_sgl_curso: 'sigla-que-nao-existe'));
    }

    public function test_lista_os_inativos_quando_pedido(): void
    {
        $id = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'ativo' => 0])->getKey();

        $this->assertContains($id, array_column((new clsPmieducarCurso)->lista(int_cod_curso: $id, int_ativo: 0), 'cod_curso'));
        $this->assertFalse((new clsPmieducarCurso)->lista(int_cod_curso: $id));
    }

    public function test_lista_um_campo_so_devolve_os_valores_soltos(): void
    {
        $id = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'nm_curso' => 'Curso Zeta'])->getKey();

        $curso = new clsPmieducarCurso;
        $curso->setCamposLista('nm_curso');

        $this->assertSame(['Curso Zeta'], $curso->lista(int_cod_curso: $id));
    }

    public function test_diz_se_o_curso_existe(): void
    {
        $id = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();

        $this->assertNotEmpty((new clsPmieducarCurso($id))->existe());
        $this->assertEmpty((new clsPmieducarCurso(0))->existe());
        $this->assertFalse((new clsPmieducarCurso)->existe());
        $this->assertFalse((new clsPmieducarCurso)->detalhe());
    }

    public function test_diz_se_o_curso_e_de_atividade_complementar(): void
    {
        $complementary = LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $this->institution,
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create([
                'ref_cod_instituicao' => $this->institution,
                'atividade_complementar' => true,
            ]),
        ]);
        $regular = LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $this->institution,
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create([
                'ref_cod_instituicao' => $this->institution,
                'atividade_complementar' => false,
            ]),
        ]);

        $this->assertTrue((new clsPmieducarCurso($complementary->getKey()))->cursoDeAtividadeComplementar());
        $this->assertFalse((new clsPmieducarCurso($regular->getKey()))->cursoDeAtividadeComplementar());
        $this->assertNull((new clsPmieducarCurso)->cursoDeAtividadeComplementar());
    }
}
