<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\LegacyStageType;
use App\Models\View\Discipline;
use Illuminate\Support\Facades\DB;

return new class() extends clsDetalhe
{
    public $titulo;

    public $cod_turma;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_ref_cod_serie;

    public $ref_ref_cod_escola;

    public $nm_turma;

    public $sgl_turma;

    public $max_aluno;

    public $multiseriada;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_turma_tipo;

    public $hora_inicial;

    public $hora_final;

    public $hora_inicio_intervalo;

    public $hora_fim_intervalo;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_instituicao_regente;

    public $ref_cod_regente;

    public function Gerar()
    {
        $this->titulo = 'Turma - Detalhe';
        $this->cod_turma = $_GET['cod_turma'];

        $dias_da_semana = [
            '' => 'Selecione',
            1 => 'Domingo',
            2 => 'Segunda',
            3 => 'Terça',
            4 => 'Quarta',
            5 => 'Quinta',
            6 => 'Sexta',
            7 => 'Sábado',
        ];

        $not_access = false;
        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $not_access = LegacySchoolClass::filter(['school_user' => $this->pessoa_logada])->where(column: 'cod_turma', operator: $this->cod_turma)->doesntExist();
        }

        $lst_obj = (new clsPmieducarTurma())->lista(
            int_cod_turma: $this->cod_turma,
            visivel: [
                'true',
                'false',
            ]
        );

        if (empty($lst_obj) || $not_access) {
            $this->simpleRedirect(url: 'educar_turma_lst.php');
        }

        $registro = array_shift(array: $lst_obj);

        foreach ($registro as $key => $value) {
            $this->$key = $value;
        }

        $registro['ref_cod_turma_tipo'] = LegacySchoolClassType::findOrFail(id: $registro['ref_cod_turma_tipo'])->nm_tipo;

        $obj_cod_instituicao = new clsPmieducarInstituicao(
            cod_instituicao: $registro['ref_cod_instituicao']
        );

        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

        $this->ref_ref_cod_escola = $registro['ref_ref_cod_escola'];
        $obj_ref_cod_escola = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

        $obj_ref_cod_curso = new clsPmieducarCurso(cod_curso: $registro['ref_cod_curso']);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
        $padrao_ano_escolar = $det_ref_cod_curso['padrao_ano_escolar'];

        $this->ref_ref_cod_serie = $registro['ref_ref_cod_serie'];
        $obj_ser = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
        $det_ser = $obj_ser->detalhe();
        $registro['ref_ref_cod_serie'] = $det_ser['nm_serie'];

        $obj_permissoes = new clsPermissoes();

        $this->addDetalhe(detalhe: ['Ano', $this->ano]);

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(
                detalhe: [
                    'Instituição',
                    $registro['ref_cod_instituicao'],
                ]
            );
        }

        if ($registro['ref_ref_cod_escola']) {
            $this->addDetalhe(
                detalhe: [
                    'Escola',
                    $registro['ref_ref_cod_escola'],
                ]
            );
        }

        if ($registro['multiseriada'] == 1) {
            $seriesDaTurma = LegacySchoolClassGrade::query()
                ->where(column: 'turma_id', operator: $this->cod_turma)
                ->with(relations: 'grade')
                ->get()
                ->map(callback: function ($turmaSerie) {
                    return $turmaSerie->grade->nm_serie;
                })
                ->implode(value: '</br>');

            $this->addDetalhe(detalhe: ['Multisseriada', 'Sim']);
            $this->addDetalhe(detalhe: ['Curso principal', $registro['ref_cod_curso']]);
            $this->addDetalhe(detalhe: ['Série principal', $registro['ref_ref_cod_serie']]);
            $this->addDetalhe(detalhe: ['Séries da turma', $seriesDaTurma]);
        } else {
            $this->addDetalhe(detalhe: ['Multisseriada', 'Não']);
            $this->addDetalhe(detalhe: ['Curso', $registro['ref_cod_curso']]);
            $this->addDetalhe(detalhe: ['Série', $registro['ref_ref_cod_serie']]);
        }

        if ($registro['ref_cod_regente']) {
            $obj_pessoa = new clsPessoa_(int_idpes: $registro['ref_cod_regente']);
            $det = $obj_pessoa->detalhe();

            $this->addDetalhe(
                detalhe: [
                    'Professor/Regente',
                    $det['nome'],
                ]
            );
        }

        if ($registro['ref_cod_turma_tipo']) {
            $this->addDetalhe(
                detalhe: [
                    'Tipo de Turma',
                    $registro['ref_cod_turma_tipo'],
                ]
            );
        }

        if ($registro['nm_turma']) {
            $this->addDetalhe(
                detalhe: [
                    'Turma',
                    $registro['nm_turma'],
                ]
            );
        }

        if ($registro['sgl_turma']) {
            $this->addDetalhe(
                detalhe: [
                    _cl(key: 'turma.detalhe.sigla'),
                    $registro['sgl_turma'],
                ]
            );
        }

        if ($registro['max_aluno']) {
            $this->addDetalhe(
                detalhe: [
                    'Máximo de Alunos',
                    $registro['max_aluno'],
                ]
            );
        }

        $this->addDetalhe(
            detalhe: [
                'Situação',
                dbBool(val: $registro['visivel']) ? 'Ativo' : 'Desativo',
            ]
        );

        if ($padrao_ano_escolar == 1) {
            if ($registro['hora_inicial']) {
                $registro['hora_inicial'] = date(format: 'H:i', timestamp: strtotime(datetime: $registro['hora_inicial']));
                $this->addDetalhe(
                    detalhe: [
                        'Hora Inicial',
                        $registro['hora_inicial'],
                    ]
                );
            }

            if ($registro['hora_final']) {
                $registro['hora_final'] = date(format: 'H:i', timestamp: strtotime(datetime: $registro['hora_final']));
                $this->addDetalhe(
                    detalhe: [
                        'Hora Final',
                        $registro['hora_final'],
                    ]
                );
            }

            if ($registro['hora_inicio_intervalo']) {
                $registro['hora_inicio_intervalo'] = date(format: 'H:i', timestamp: strtotime(datetime: $registro['hora_inicio_intervalo']));
                $this->addDetalhe(
                    detalhe: [
                        'Hora Início Intervalo',
                        $registro['hora_inicio_intervalo'],
                    ]
                );
            }

            if ($registro['hora_fim_intervalo']) {
                $registro['hora_fim_intervalo'] = date(format: 'H:i', timestamp: strtotime(datetime: $registro['hora_fim_intervalo']));
                $this->addDetalhe(
                    detalhe: [
                        'Hora Fim Intervalo',
                        $registro['hora_fim_intervalo'],
                    ]
                );
            }

            if (is_string(value: $registro['dias_semana']) && !empty($registro['dias_semana'])) {
                $registro['dias_semana'] = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $registro['dias_semana']));
                $diasSemana = '';
                foreach ($registro['dias_semana'] as $dia) {
                    $diasSemana .= $dias_da_semana[$dia] . '<br>';
                }
                $this->addDetalhe(
                    detalhe: [
                        'Dia da Semana',
                        $diasSemana,
                    ]
                );
            }
        } elseif ($padrao_ano_escolar == 0) {
            $obj = new clsPmieducarTurmaModulo();
            $obj->setOrderby(strNomeCampo: 'sequencial ASC');
            $lst = $obj->lista(int_ref_cod_turma: $this->cod_turma);

            if ($lst) {
                $tabela = '
          <table>
            <tr align="center">
              <td bgcolor="#f5f9fd "><b>Nome</b></td>
              <td bgcolor="#f5f9fd "><b>Data Início</b></td>
              <td bgcolor="#f5f9fd "><b>Data Fim</b></td>
              <td bgcolor="#f5f9fd "><b>Dias Letivos</b></td>
            </tr>';

                $cont = 0;

                foreach ($lst as $valor) {
                    if (($cont % 2) == 0) {
                        $color = ' bgcolor="#f5f9fd " ';
                    } else {
                        $color = ' bgcolor="#FFFFFF" ';
                    }

                    $nm_modulo = LegacyStageType::find($valor['ref_cod_modulo'])->nm_tipo;

                    $valor['data_inicio'] = dataFromPgToBr(data_original: $valor['data_inicio']);
                    $valor['data_fim'] = dataFromPgToBr(data_original: $valor['data_fim']);

                    $tabela .= sprintf(
                        '
            <tr>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=center>%s</td>
            </tr>',
                        $color,
                        $nm_modulo,
                        $color,
                        $valor['data_inicio'],
                        $color,
                        $valor['data_fim'],
                        $color,
                        $valor['dias_letivos']
                    );

                    $cont++;
                }

                $tabela .= '</table>';
            }

            if ($tabela) {
                $this->addDetalhe(
                    detalhe: [
                        'Módulo',
                        $tabela,
                    ]
                );
            }

            if (is_string(value: $registro['dias_semana']) && !empty($registro['dias_semana'])) {
                $registro['dias_semana'] = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $registro['dias_semana']));
                $diasSemana = '';
                foreach ($registro['dias_semana'] as $dia) {
                    $diasSemana .= $dias_da_semana[$dia] . '<br>';
                }
                $this->addDetalhe(
                    detalhe: [
                        'Dia da Semana',
                        $diasSemana,
                    ]
                );
            }
        }

        if ($this->multiseriada == 1) {
            $this->montaListaComponentesMulti();
        } else {
            $this->montaListaComponentes();
        }

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_turma_cad.php';
            $this->url_editar = 'educar_turma_cad.php?cod_turma=' . $registro['cod_turma'];

            $this->array_botao[] = 'Reclassificar alunos alfabeticamente';
            $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente reclassificar os alunos alfabeticamente?\\nAo utilizar esta opção para esta turma, a ordenação dos alunos no diário e em relatórios que é controlada por ordem de chegada após a data de fechamento da turma (campo Data de fechamento), passará a ter o controle novamente alfabético, desconsiderando a data de fechamento.\"))reclassifica_matriculas({$registro['cod_turma']})";

            $this->array_botao[] = 'Reclassificar alunos por data base';
            $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente reclassificar os alunos por data base?\\nAo utilizar esta opção para esta turma, a ordenação dos alunos no diário e em relatórios será reordenada para verificar a existência de data base, assim como validação das matrículas de dependência, respeitando a data de enturmação das matrículas e não somente ordenação alfabética.\"))ordena_matriculas_por_data_base({$registro['cod_turma']})";

            $this->array_botao[] = 'Editar sequência de alunos na turma';
            $this->array_botao_url_script[] = sprintf('go("educar_ordenar_alunos_turma.php?cod_turma=%d");', $registro['cod_turma']);

            $this->array_botao[] = 'Lançar pareceres da turma';
            $this->array_botao_url_script[] = sprintf('go("educar_parecer_turma_cad.php?cod_turma=%d");', $registro['cod_turma']);

            $doesntExist = \App\Models\LegacySchoolClassTeacher::query()
                ->where('ano', $registro['ano'])
                ->where('turma_id', $registro['cod_turma'])
                ->doesntExist();

            if ($doesntExist) {
                $this->array_botao[] = 'Copiar vínculo de servidores';
                $this->array_botao_url_script[] = sprintf('go("copia_vinculos_servidores_cad.php?cod_turma=%d");', $registro['cod_turma']);
            }
        }

        $this->url_cancelar = 'educar_turma_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/TurmaDet.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function montaListaComponentes()
    {
        $componentes = Discipline::getBySchoolClassAndGrade($this->cod_turma, $this->ref_ref_cod_serie);

        if ($componentes->isNotEmpty()) {
            $disciplinas = '<table id="table-disciplines">';
            $disciplinas .= '<tr>';
            $disciplinas .= '<td><b>Nome</b></td>';
            $disciplinas .= '<td><b>Carga horária(h)</b></td>';
            $disciplinas .= '</tr>';

            foreach ($componentes as $componente) {
                $disciplinas .= '<tr>';
                $disciplinas .= "<td>{$componente->name}</td>";
                $disciplinas .= "<td style='text-align: center'>{$componente->workload}</td>";
                $disciplinas .= '</tr>';
            }
            $disciplinas .= '</table>';
        } else {
            $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
        }
        $this->addDetalhe(detalhe: ['Componentes curriculares',
            '<a id="show-detail" href=\'javascript:trocaDisplay("det_pree");\' >Mostrar detalhe</a><div id=\'det_pree\' name=\'det_pree\' style=\'display:none;\'>' . $disciplinas . '</div>']);
    }

    public function montaListaComponentesMulti()
    {
        $this->tabela3 = '';
        $componentes = $this->getComponentesTurmaMulti(turmaId: $this->cod_turma);
        if ($componentes->isNotEmpty()) {
            $disciplinas = '<table id="table-disciplines">';
            $disciplinas .= '<tr>';
            $disciplinas .= '<td><b>Nome</b></td>';
            $disciplinas .= '<td><b>Série</b></td>';
            $disciplinas .= '<td><b>Carga horária(h)</b></td>';
            $disciplinas .= '</tr>';
            foreach ($componentes as $componente) {
                $disciplinas .= '<tr>';
                $disciplinas .= "<td>{$componente->name}</td>";
                $disciplinas .= "<td>{$componente->grade}</td>";
                $disciplinas .= "<td style='text-align: center'>{$componente->workload}</td>";
                $disciplinas .= '</tr>';
            }
            $disciplinas .= '</table>';
        } else {
            $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
        }
        $this->addDetalhe(detalhe: ['Componentes curriculares',
            '<a id="show-detail" href=\'javascript:trocaDisplay("det_pree");\' >Mostrar detalhe</a><div id=\'det_pree\' name=\'det_pree\' style=\'display:none;\'>' . $disciplinas . '</div>']);
    }

    public function makeCss()
    {
        return file_get_contents(filename: __DIR__ . '/styles/extra/educar-turma-det.css');
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-turma-det.js');
    }

    public function getComponentesTurma()
    {
        return LegacySchoolGradeDiscipline::whereGrade($this->ref_ref_cod_serie)
            ->whereSchool(school: $this->ref_ref_cod_escola)
            ->whereYearEq($this->ano)
            ->active()
            ->get();
    }

    public function getComponentesTurmaMulti($turmaId)
    {
        $componentes = DB::table(table: 'pmieducar.turma as t')
            ->selectRaw(expression: 'cc.id, cc.nome as name,coalesce(esd.carga_horaria, ccae.carga_horaria)::int AS workload,STRING_AGG(s.nm_serie, \', \' order by nm_serie) as grade')
            ->join(table: 'pmieducar.turma_serie as ts', first: 'ts.turma_id', operator: '=', second: 't.cod_turma')
            ->leftJoin(table: 'pmieducar.serie as s', first: 's.cod_serie', operator: 'ts.serie_id')
            ->join(table: 'pmieducar.escola_serie as es', first: function ($join) {
                $join->on('es.ref_cod_serie', '=', 'ts.serie_id');
                $join->on('es.ref_cod_escola', '=', 't.ref_ref_cod_escola');
            })
            ->join(table: 'pmieducar.escola_serie_disciplina as esd', first: function ($join) {
                $join->on('esd.ref_ref_cod_serie', '=', 'es.ref_cod_serie');
                $join->on('esd.ref_ref_cod_escola', '=', 'es.ref_cod_escola');
            })
            ->join(table: 'modules.componente_curricular as cc', first: 'cc.id', operator: '=', second: 'esd.ref_cod_disciplina')
            ->join(table: 'modules.componente_curricular_ano_escolar as ccae', first: function ($join) {
                $join->on('ccae.componente_curricular_id', '=', 'cc.id');
                $join->on('ccae.ano_escolar_id', '=', 'es.ref_cod_serie');
            })
            ->where(column: 't.cod_turma', operator: $turmaId)
            ->whereRaw(sql: 't.ano = ANY(esd.anos_letivos)')
            ->where(column: 't.multiseriada', operator: 1)
            ->groupBy([
                'cc.id',
                'workload',
                'name',
            ])
            ->orderBy(column: 'workload', direction: 'desc')
            ->get();

        return $componentes->each(callback: function ($item) use ($componentes) {
            $item->order = $componentes->where(key: 'id', operator: $item->id)->max(callback: 'workload');
        })->sortBy(callback: [
            ['order', 'desc'],
            ['id', 'asc'],
            ['name', 'asc'],
        ]);
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = 586;
    }
};
