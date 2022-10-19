<?php

return new class extends clsDetalhe {
    public $titulo;
    public $id;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;
    public $ref_cod_infra_predio_comodo;
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
        $this->titulo = 'Plano de aula AEE - Detalhe';
        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesPlanejamentoAulaAee($this->id);
        $registro = $tmp_obj->detalhe();

        if (!$registro['detalhes']) {
            $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_lst.php');
        }

        $obj = new clsPmieducarTurma($registro['detalhes']['ref_cod_turma']);
        $resultado = $obj->getGrau();

        if ($registro['detalhes']['id']) {
            $this->addDetalhe(
                [
                    'ID',
                    $registro['detalhes']['id']
                ]
            );
        }

        if ($registro['detalhes']['data_inicial']) {
            $this->addDetalhe(
                [
                    'Data inicial',
                    dataToBrasil($registro['detalhes']['data_inicial'])
                ]
            );
        }

        if ($registro['detalhes']['data_final']) {
            $this->addDetalhe(
                [
                    'Data final',
                    dataToBrasil($registro['detalhes']['data_final'])
                ]
            );
        }

        if ($registro['detalhes']['escola']) {
            $this->addDetalhe(
                [
                    'Escola',
                    $registro['detalhes']['escola']
                ]
            );
        }

        if ($registro['detalhes']['professor']) {
            $this->addDetalhe(
                [
                    'Professor',
                    $registro['detalhes']['professor']
                ]
            );
        }

        if ($registro['detalhes']['ref_cod_turma']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['detalhes']['turma']
                ]
            );
        }

        if ($registro['detalhes']['ref_cod_matricula']) {
            $this->addDetalhe(
                [
                    'Aluno',
                    $registro['detalhes']['aluno']
                ]
            );
        }

        if ($registro['componentesCurriculares']) {
            $nomeComponenteCurricular = '';
            foreach ($registro['componentesCurriculares'] as $componenteCurricular) {
                $nomeComponenteCurricular .= $componenteCurricular['nome'].'<br>';
            }

            $this->addDetalhe(
                [
                    $resultado == 0 ? 'Componente curricular' : 'Campo de experiência',
                    $nomeComponenteCurricular
                ]
            );
        }

        if ($registro['detalhes']['etapa'] && $registro['detalhes']['fase_etapa']) {
            $this->addDetalhe(
                [
                    'Etapa',
                    $registro['detalhes']['fase_etapa'] . "º " . $registro['detalhes']['etapa']
                ]
            );
        }

        if (is_array($registro['bnccs'])) {
            $this->montaListaBNCC($registro['bnccs']);
        }

        if (is_array($registro['especificacoes']) && $registro['especificacoes'] != null) {
            $this->montaListaEspecificacoes($registro['especificacoes']);
        }

        if (is_array($registro['conteudos']) && $registro['conteudos'] != null) {
            $this->montaListaConteudos($registro['conteudos']);
        }

        if ($registro['detalhes']['ddp']) {
            $this->addDetalhe(
                [
                    'Metodologia',
                    $registro['detalhes']['ddp']
                ]
            );
        }

        if ($registro['detalhes']['recursos_didaticos']) {
            $this->addDetalhe(
                [
                    'Recursos didáticos',
                    $registro['detalhes']['recursos_didaticos']
                ]
            );
        }

        if ($registro['detalhes']['outros']) {
            $this->addDetalhe(
                [
                    'Outros',
                    $registro['detalhes']['outros']
                ]
            );
        }
       

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_professores_planejamento_de_aula_aee_cad.php';

            $data_agora = new DateTime('now');
            $data_agora = new \DateTime($data_agora->format('Y-m-d'));

            $turma = $registro['detalhes']['ref_cod_turma'];
            $sequencia = $registro['detalhes']['fase_etapa'];
            $obj = new clsPmieducarTurmaModulo();

            $data = $obj->pegaPeriodoLancamentoNotasFaltasAee($turma, $sequencia);
            if ($data['inicio'] != null && $data['fim'] != null) {
                $data['inicio'] = explode(',', $data['inicio']);
                $data['fim'] = explode(',', $data['fim']);

                array_walk($data['inicio'], function(&$data_inicio, $key) {
                    $data_inicio = new \DateTime($data_inicio);
                });

                array_walk($data['fim'], function(&$data_fim, $key) {
                    $data_fim = new \DateTime($data_fim);
                });
            } else {
                $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
                $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));
            }

            $podeEditar = false;
            if (is_array($data['inicio']) && is_array($data['fim'])) {
                for ($i=0; $i < count($data['inicio']); $i++) {
                    $data_inicio = $data['inicio'][$i];
                    $data_fim = $data['fim'][$i];

                    $podeEditar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                    if ($podeEditar) break;
                }
            } else {
                $podeEditar = $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
            }

             if ($podeEditar)
                 $this->url_editar = 'educar_professores_planejamento_de_aula_aee_cad.php?id=' . $registro['detalhes']['id'];

        }

        $this->url_cancelar = 'educar_professores_planejamento_de_aula_aee_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do plano de aula AEE', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);

//        $this->addBotao('Excluir', "");

        //$this->addBotao('Copiar plano de aula', "/intranet/educar_professores_planejamento_de_aula_cad.php?id={$this->getRequest()->id}&copy=true");
    }

    function montaListaBNCC ($bnccs) {

        $this->tabela .= ' <tr>';
        $this->tabela .= ' <td class="formmdtd"><span style="display: block; float: left; width: 100px; font-weight: bold;">Código</span></td>';
        $this->tabela .= ' <td class="formmdtd"><span style="display: block; float: left; width: 700px; font-weight: bold;">Habilidade</span></td>';
        $this->tabela .= ' </tr>';

        for ($i=0; $i < count($bnccs); $i++) {
            $this->tabela .= ' <tr>';
            $this->tabela .= "  <td class='formmdtd'><span style='display: block; float: left; width: 100px; margin-bottom: 10px'>{$bnccs[$i][codigo]}</span></td>";
            $this->tabela .= "  <td class='formmdtd'><span style='display: block; float: left; width: 700px; margin-bottom: 10px'>{$bnccs[$i][descricao]}</span></td>";
            $this->tabela .= ' </tr>';
        }

        $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
        $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $bncc .= '</table>';

        $this->addDetalhe(
            [
                'Objetivos de aprendizagem/habilidades (BNCC)',
                $bncc
            ]
        );
    }

    function montaListaEspecificacoes ($especificacoes) {
        for ($i=0; $i < count($especificacoes); $i++) {
            $this->tabela3 .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            $this->tabela3 .= "  <span style='display: block; float: left; width: 750px'>{$especificacoes[$i][especificacao]}</span>";
            $this->tabela3 .= '  </div>';
            $this->tabela3 .= '  <br style="clear: left" />';
        }

        $especificacao  = '<table cellspacing="0" cellpadding="0" border="0">';
        $especificacao .= sprintf('<tr align="left"><td class="formlttd">%s</td></tr>', $this->tabela3);
        $especificacao .= '</table>';

        $this->addDetalhe(
            [
                'Especificações',
                $especificacao
            ]
        );
    }

    function montaListaConteudos ($conteudos) {
        for ($i=0; $i < count($conteudos); $i++) {
            $this->tabela2 .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            $this->tabela2 .= "  <span style='display: block; float: left; width: 750px'>{$conteudos[$i][conteudo]}</span>";
            $this->tabela2 .= '  </div>';
            $this->tabela2 .= '  <br style="clear: left" />';
        }

        $conteudo  = '<table cellspacing="0" cellpadding="0" border="0">';
        $conteudo .= sprintf('<tr align="left"><td class="formlttd">%s</td></tr>', $this->tabela2);
        $conteudo .= '</table>';

        $this->addDetalhe(
            [
                'Conteúdos',
                $conteudo
            ]
        );
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaAeeExclusaoTemp.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular()
    {
        $this->title = 'Plano de aula AEE - Detalhe';
        $this->processoAp = 58;
    }
};
