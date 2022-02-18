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
        $this->titulo = 'Planejamento de aula - Detalhe';
        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesPlanejamentoAula($this->id);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
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

        if ($registro['detalhes']['turma_id']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['detalhes']['turma']
                ]
            );
        }

        if ($registro['detalhes']['componente_curricular']) {
            $this->addDetalhe(
                [
                    'Componente curricular',
                    $registro['detalhes']['componente_curricular']
                ]
            );
        } else {
            $this->addDetalhe(
                [
                    'Componente curricular',
                    '—'
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
        
        if (is_array($registro['bnccs']) && $registro['bnccs'] != null) {
            $this->montaListaBNCC($registro['bnccs']);
        }

        if (is_array($registro['conteudos']) && $registro['conteudos'] != null) {
            $this->montaListaConteudos($registro['conteudos']);
        }

        if ($registro['detalhes']['ddp']) {
            $this->addDetalhe(
                [
                    'Desdobramento didático pedagógico',
                    $registro['detalhes']['ddp']
                ]
            );
        }

        if ($registro['detalhes']['atividades']) {
            $this->addDetalhe(
                [
                    'Atividades',
                    $registro['detalhes']['atividades']
                ]
            );
        }

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_professores_planejamento_de_aula_cad.php';

            $data_agora = new DateTime('now');
            $data_agora = new \DateTime($data_agora->format('Y-m-d'));

            $turma = $registro['detalhes']['ref_cod_turma'];
            $sequencia = $registro['detalhes']['fase_etapa'];
            $obj = new clsPmieducarTurmaModulo();

            $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
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
                $this->url_editar = 'educar_professores_planejamento_de_aula_cad.php?id=' . $registro['detalhes']['id'];
        }

        $this->url_cancelar = 'educar_professores_planejamento_de_aula_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da frequência', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }

    function montaListaBNCC ($bnccs) {
        $this->tabela .= ' <div style="margin-bottom: 10px;">';
        $this->tabela .= ' <span style="display: block; float: left; width: 100px; font-weight: bold">Código</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 700px; font-weight: bold">Habilidade</span>';
        $this->tabela .= ' </div>';
        $this->tabela .= ' <br style="clear: left" />';

        for ($i=0; $i < count($bnccs); $i++) {
            $this->tabela .= "  <span style='display: block; float: left; width: 100px; margin-bottom: 10px'>{$bnccs[$i][bncc][codigo]}</span>";
            $this->tabela .= "  <span style='display: block; float: left; width: 700px; margin-bottom: 10px'>{$bnccs[$i][bncc][habilidade]}</span>";
        }
        
        $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
        $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $bncc .= '</table>';

        $this->addDetalhe(
            [
                'Objetivos de aprendizagem/habilidades',
                $bncc
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

        $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
        $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela2);
        $bncc .= '</table>';

        $this->addDetalhe(
            [
                'Conteúdos',
                $bncc
            ]
        );
    }

    public function Formular()
    {
        $this->title = 'Frequência - Detalhe';
        $this->processoAp = 58;
    }
};
