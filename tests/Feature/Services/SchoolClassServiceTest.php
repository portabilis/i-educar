<?php

namespace Tests\Feature\Services;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolClassServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testShoudBeCreateASchoolClass()
    {
        $arr = [
            'tipoacao' => 'Editar',
            'obrigar_campos_censo' => '1',
            'ref_cod_escola_' => '10',
            'ano_letivo_' => '2021',
            'dependencia_administrativa' => '3',
            'modalidade_curso' => '1',
            'retorno' => 'Editar',
            'horario_funcionamento_turma' => null,
            'disciplinas_' => '%3Cdiv+id%3D%27disciplinas%27%3E%3C%2Fdiv%3E',
            'etapas_cabecalho' => null,
            'padrao_ano_escolar' => '0',
            'ref_cod_instituicao' => '1',
            'ref_cod_escola' => '10',
            'multiseriada' => '1',
            'mult_curso_id' =>
                [
                    1 => '4',
                    2 => '4',
                ],
            'mult_serie_id' =>
                [
                    1 => '21',
                    2 => '52',
                ],
            'mult_boletim_id' =>
                [
                    1 => '1',
                    2 => '1',
                ],
            'mult_boletim_diferenciado_id' =>
                [
                    1 => '1',
                    2 => '1',
                ],
            'mult_padrao_ano_escolar' =>
                [
                    1 => '0',
                    2 => '0',
                ],
            'ref_cod_curso' => '4',
            'ref_cod_serie' => '21',
            'ano_letivo' => '2021',
            'ref_cod_infra_predio_comodo' => null,
            'ref_cod_regente' => null,
            'ref_cod_turma_tipo' => '3',
            'nm_turma' => 't7',
            'sgl_turma' => 't7',
            'max_aluno' => '10',
            'ref_cod_disciplina_dispensada' => null,
            'visivel' => '1',
            'tipo_mediacao_didatico_pedagogico' => '3',
            'turma_turno_id' => '1',
            'tipo_boletim' => '1',
            'tipo_boletim_diferenciado' => '1',
            'ref_cod_modulo' => '3',
            'data_inicio' =>
                [
                    0 => '01/03/2021',
                    1 => '01/06/2021',
                    2 => '17/09/2021',
                ],
            'data_fim' =>
                [
                    0 => '31/05/2021',
                    1 => '16/09/2021',
                    2 => '17/12/2021',
                ],
            'dias_letivos' =>
                [
                    0 => '100',
                    1 => '101',
                    2 => '102',
                ],
            'codigo_inep_educacenso' => null,
            'tipo_atendimento' => '5',
            'etapa_educacenso' => null,
        ];

        $this->actingAs(User::query()->orderBy('cod_usuario')->first());
        $response = $this->post('/turma', $arr);

        $response->assertStatus(200);
    }

    public function testShoudNotBeCreateASchoolClassWithoutPeriod()
    {
        $arr = [
            'tipoacao' => 'Editar',
            'obrigar_campos_censo' => '1',
            'ref_cod_escola_' => '10',
            'ano_letivo_' => '2021',
            'dependencia_administrativa' => '3',
            'modalidade_curso' => '1',
            'retorno' => 'Editar',
            'horario_funcionamento_turma' => null,
            'disciplinas_' => '%3Cdiv+id%3D%27disciplinas%27%3E%3C%2Fdiv%3E',
            'etapas_cabecalho' => null,
            'padrao_ano_escolar' => '0',
            'ref_cod_instituicao' => '1',
            'ref_cod_escola' => '10',
            'multiseriada' => '1',
            'mult_curso_id' =>
                [
                    1 => '4',
                    2 => '4',
                ],
            'mult_serie_id' =>
                [
                    1 => '21',
                    2 => '52',
                ],
            'mult_boletim_id' =>
                [
                    1 => '1',
                    2 => '1',
                ],
            'mult_boletim_diferenciado_id' =>
                [
                    1 => '1',
                    2 => '1',
                ],
            'mult_padrao_ano_escolar' =>
                [
                    1 => '0',
                    2 => '0',
                ],
            'ref_cod_curso' => '4',
            'ref_cod_serie' => '21',
            'ano_letivo' => '2021',
            'ref_cod_infra_predio_comodo' => null,
            'ref_cod_regente' => null,
            'ref_cod_turma_tipo' => '3',
            'nm_turma' => 't7',
            'sgl_turma' => 't7',
            'max_aluno' => '10',
            'ref_cod_disciplina_dispensada' => null,
            'visivel' => '1',
            'tipo_mediacao_didatico_pedagogico' => '3',
            'turma_turno_id' => '1',
            'tipo_boletim' => '1',
            'tipo_boletim_diferenciado' => '1',
            'ref_cod_modulo' => '3',
            'codigo_inep_educacenso' => null,
            'tipo_atendimento' => '5',
            'etapa_educacenso' => null,
        ];

        $this->actingAs(User::query()->orderBy('cod_usuario')->first());
        $response = $this->post('/turma', $arr);

        $response->assertStatus(422);
    }
}
