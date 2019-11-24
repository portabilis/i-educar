<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/CustomLabel.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Turma');
        $this->processoAp = 586;
    }
}

class indice extends clsDetalhe
{
    var $titulo;
    var $cod_turma;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_ref_cod_serie;
    var $ref_ref_cod_escola;
    var $ref_cod_infra_predio_comodo;
    var $nm_turma;
    var $sgl_turma;
    var $max_aluno;
    var $multiseriada;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_turma_tipo;
    var $hora_inicial;
    var $hora_final;
    var $hora_inicio_intervalo;
    var $hora_fim_intervalo;
    var $ref_cod_instituicao;
    var $ref_cod_curso;
    var $ref_cod_instituicao_regente;
    var $ref_cod_regente;

    function Gerar()
    {
        $this->titulo = 'Turma - Detalhe';
        $this->addBanner(
            'imagens/nvp_top_intranet.jpg',
            'imagens/nvp_vert_intranet.jpg',
            'Intranet'
        );

        $this->cod_turma = $_GET['cod_turma'];

        $dias_da_semana = [
            '' => 'Selecione',
            1 => 'Domingo',
            2 => 'Segunda',
            3 => 'Terça',
            4 => 'Quarta',
            5 => 'Quinta',
            6 => 'Sexta',
            7 => 'Sábado'
        ];

        $tmp_obj = new clsPmieducarTurma();
        $lst_obj = $tmp_obj->lista(
            $this->cod_turma,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            [
                'true',
                'false'
            ]
        );

        $registro = array_shift($lst_obj);

        foreach ($registro as $key => $value) {
            $this->$key = $value;
        }

        if (!$registro) {
            $this->simpleRedirect('educar_turma_lst.php');
        }

            $obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo(
                $registro['ref_cod_turma_tipo']
            );

            $det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
            $registro['ref_cod_turma_tipo'] = $det_ref_cod_turma_tipo['nm_tipo'];

            $obj_ref_cod_infra_predio_comodo = new clsPmieducarInfraPredioComodo(
                $registro['ref_cod_infra_predio_comodo']
            );

            $det_ref_cod_infra_predio_comodo = $obj_ref_cod_infra_predio_comodo->detalhe();
            $registro['ref_cod_infra_predio_comodo'] = $det_ref_cod_infra_predio_comodo['nm_comodo'];

            $obj_cod_instituicao = new clsPmieducarInstituicao(
                $registro['ref_cod_instituicao']
            );

            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

            $this->ref_ref_cod_escola = $registro['ref_ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

            $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
            $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
            $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
            $padrao_ano_escolar = $det_ref_cod_curso['padrao_ano_escolar'];

            $this->ref_ref_cod_serie = $registro['ref_ref_cod_serie'];
            $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();
            $registro['ref_ref_cod_serie'] = $det_ser['nm_serie'];

        $obj_permissoes = new clsPermissoes();

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(
                [
                    'Instituição',
                    $registro['ref_cod_instituicao']
                ]
            );
        }

        if ($registro['ref_ref_cod_escola']) {
            $this->addDetalhe(
                [
                    'Escola',
                    $registro['ref_ref_cod_escola']
                ]
            );
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(
                [
                    'Curso',
                    $registro['ref_cod_curso']
                ]
            );
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(
                [
                    'Série',
                    $registro['ref_ref_cod_serie']
                ]
            );
        }

        if ($registro['ref_cod_regente']) {
            $obj_pessoa = new clsPessoa_($registro['ref_cod_regente']);
            $det = $obj_pessoa->detalhe();

            $this->addDetalhe(
                [
                    'Professor/Regente',
                    $det['nome']
                ]
            );
        }

        if ($registro['ref_cod_infra_predio_comodo']) {
            $this->addDetalhe(
                [
                    'Sala',
                    $registro['ref_cod_infra_predio_comodo']
                ]
            );
        }

        if ($registro['ref_cod_turma_tipo']) {
            $this->addDetalhe(
                [
                    'Tipo de Turma',
                    $registro['ref_cod_turma_tipo']
                ]
            );
        }

        if ($registro['nm_turma']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['nm_turma']
                ]
            );
        }

        if ($registro['sgl_turma']) {
            $this->addDetalhe(
                [
                    _cl('turma.detalhe.sigla'),
                    $registro['sgl_turma']
                ]
            );
        }

        if ($registro['max_aluno']) {
            $this->addDetalhe(
                [
                    'Máximo de Alunos',
                    $registro['max_aluno']
                ]
            );
        }

        $this->addDetalhe(
            [
                'Situação',
                dbBool($registro['visivel']) ? 'Ativo' : 'Desativo'
            ]
        );

        if ($registro['multiseriada'] == 1) {
            if ($registro['multiseriada'] == 1) {
                $registro['multiseriada'] = 'sim';
            } else {
                $registro['multiseriada'] = 'não';
            }

            $this->addDetalhe(array('Multi-Seriada', $registro['multiseriada']));

            $obj_serie_mult = new clsPmieducarSerie($registro['ref_ref_cod_serie_mult']);
            $det_serie_mult = $obj_serie_mult->detalhe();

            $this->addDetalhe(array('Série Multi-Seriada', $det_serie_mult['nm_serie']));
        }

        if ($padrao_ano_escolar == 1) {
            if ($registro['hora_inicial']) {
                $registro['hora_inicial'] = date('H:i', strtotime($registro['hora_inicial']));
                $this->addDetalhe(
                    [
                        'Hora Inicial',
                        $registro['hora_inicial']
                    ]
                );
            }

            if ($registro['hora_final']) {
                $registro['hora_final'] = date('H:i', strtotime($registro['hora_final']));
                $this->addDetalhe(
                    [
                        'Hora Final',
                        $registro['hora_final']
                    ]
                );
            }

            if ($registro['hora_inicio_intervalo']) {
                $registro['hora_inicio_intervalo'] = date('H:i', strtotime($registro['hora_inicio_intervalo']));
                $this->addDetalhe(
                    [
                        'Hora Início Intervalo',
                        $registro['hora_inicio_intervalo']
                    ]
                );
            }

            if ($registro['hora_fim_intervalo']) {
                $registro['hora_fim_intervalo'] = date('H:i', strtotime($registro['hora_fim_intervalo']));
                $this->addDetalhe(
                    [
                        'Hora Fim Intervalo',
                        $registro['hora_fim_intervalo']
                    ]
                );
            }

            if (is_string($registro['dias_semana']) && !empty($registro['dias_semana'])) {
                $registro['dias_semana'] = explode(',', str_replace(array('{', "}"), '', $registro['dias_semana']));
                foreach ($registro['dias_semana'] as $key => $dia) {
                    $diasSemana .= $dias_da_semana[$dia] . '<br>';
                }
                $this->addDetalhe(
                    [
                        'Dia da Semana',
                        $diasSemana
                    ]
                );
            }
        } elseif ($padrao_ano_escolar == 0) {
            $obj = new clsPmieducarTurmaModulo();
            $obj->setOrderby('sequencial ASC');
            $lst = $obj->lista($this->cod_turma);

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

                    $obj_modulo = new clsPmieducarModulo($valor['ref_cod_modulo']);
                    $det_modulo = $obj_modulo->detalhe();
                    $nm_modulo = $det_modulo['nm_tipo'];

                    $valor['data_inicio'] = dataFromPgToBr($valor['data_inicio']);
                    $valor['data_fim'] = dataFromPgToBr($valor['data_fim']);

                    $tabela .= sprintf('
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
                    [
                        'Módulo',
                        $tabela
                    ]
                );
            }

            if (is_string($registro['dias_semana']) && !empty($registro['dias_semana'])) {
                $registro['dias_semana'] = explode(',', str_replace(['{', "}"], '', $registro['dias_semana']));
                foreach ($registro['dias_semana'] as $key => $dia) {
                    $diasSemana .= $dias_da_semana[$dia] . '<br>';
                }
                $this->addDetalhe(
                    [
                        'Dia da Semana',
                        $diasSemana
                    ]
                );
            }
        }

        $this->montaListaComponentes();

        if ($obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7)) {
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
        }

        $this->url_cancelar = 'educar_turma_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da turma', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/TurmaDet.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    function montaListaComponentes(){
        $this->tabela3    = '';

        try {
            $lista = App_Model_IedFinder::getEscolaSerieDisciplina(
                $this->ref_ref_cod_serie,
                $this->ref_ref_cod_escola,
                null,
                null,
                null,
                true,
                $this->ano
            );
        } catch (Throwable $e) {
            $this->mensagem = $e->getMessage();
            return;
        }

        // Instancia o mapper de turma
        $componenteTurmaMapper = new ComponenteCurricular_Model_TurmaDataMapper();
        $componentesTurma = [];

        if (isset($this->cod_turma) && is_numeric($this->cod_turma)) {
            $componentesTurma = $componenteTurmaMapper->findAll(
                [],
                ['turma' => $this->cod_turma]
            );
        }

        $componentes = [];
        foreach ($componentesTurma as $componenteTurma) {
            $componentes[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
        }
        unset($componentesTurma);
        $this->escola_serie_disciplina = [];

        if (is_array($componentes) && !empty($componentes)) {
            $lista = array_intersect_key($lista, $componentes);
        }

        if (is_array($lista) && count($lista)) {
            $this->tabela3 .= '<div style="margin-bottom: 10px;">';
            $this->tabela3 .= '  <span style="display: block; float: left; width: 250px; font-weight: bold">Nome</span>';
            $this->tabela3 .= '  <span style="display: block; float: left; width: 100px; font-weight: bold">Carga horária</span>';
            $this->tabela3 .= '</div>';
            $this->tabela3 .= '<br style="clear: left" />';

            foreach ($lista as $registro) {

                if (!is_null($componentes[$registro->id]->cargaHoraria) || 0 != $componentes[$registro->id]->cargaHoraria) {
                    $registro->cargaHoraria = $componentes[$registro->id]->cargaHoraria;
                }

                $this->tabela3 .= '<div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
                $this->tabela3 .= "  <span style='display: block; float: left; width: 250px'>{$registro}</span>";
                $this->tabela3 .= "  <span style='display: block; float: left; width: 100px'>{$registro->cargaHoraria}</span>";
                $this->tabela3 .= '</div>';
                $this->tabela3 .= '<br style="clear: left" />';

                $registro->cargaHoraria = '';
            }

            $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
            $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela3);
            $disciplinas .= '</table>';
        } else {
            $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
        }
        $this->addDetalhe(
            [
                'Componentes curriculares',
                $disciplinas
            ]
        );
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
