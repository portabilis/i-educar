<?php

return new class extends clsDetalhe {
    public $titulo;
    public $id_freq;
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
        $this->titulo = 'Frequência - Detalhe';
        $this->id_freq = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesFrequencia($this->id_freq);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }


        if ($registro['detalhes']['data']) {
            $this->addDetalhe(
                [
                    'Data',
                    dataToBrasil($registro['detalhes']['data'])
                ]
            );
        }

        if ($registro['detalhes']['turma']) {
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

        $this->montaListaFrequenciaAlunos(
            $registro['matriculas']['refs_cod_matricula'],
            $registro['matriculas']['justificativas'],
            $registro['alunos']
        );

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_professores_frequencia_cad.php';

            $data_agora = new DateTime('now');
            $data_agora = new \DateTime($data_agora->format('Y-m-d'));

            $turma = $registro['detalhes']['cod_turma'];
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
                $this->url_editar = 'educar_professores_frequencia_cad.php?id=' . $registro['detalhes']['id'];
        }

        $this->url_cancelar = 'educar_professores_frequencia_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da frequência', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }

    function montaListaFrequenciaAlunos ($matriculas, $justificativas, $alunos) {
        if (is_string($matriculas) && !empty($matriculas)) {
            $matriculas = explode(',', $matriculas);
            $justificativas = explode(',', $justificativas);

            for ($i = 0; $i < count($matriculas); $i++) {
                $alunos[$matriculas[$i]]['presenca'] = true;
                $alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
            }
        }

        $this->tabela .= ' <div style="margin-bottom: 10px;">';
        $this->tabela .= ' <span style="display: block; float: left; width: 300px; font-weight: bold">Nome</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 100px; font-weight: bold">Presença</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 300px; font-weight: bold">Justificativa</span>';
        $this->tabela .= ' </div>';
        $this->tabela .= ' <br style="clear: left" />';

        foreach ($alunos as $aluno) {
            $checked = !$aluno['presenca'] ? "checked='true'" : '';

            $this->tabela .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            $this->tabela .= "  <span style='display: block; float: left; width: 300px'>{$aluno['nome']}</span>";

            $this->tabela .= "  <label style='display: block; float: left; width: 100px;'>
                                    <input type='checkbox' disabled {$checked}>
                                </label>";
            $this->tabela .= "  <span style='display: block; float: left; width: 300px'>{$aluno['justificativa']}</span>";

            $this->tabela .= '  </div>';
            $this->tabela .= '  <br style="clear: left" />';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $disciplinas .= '</table>';

        $this->addDetalhe(
            [
                'Alunos',
                $disciplinas
            ]
        );
    }

    public function Formular()
    {
        $this->title = 'Frequência - Detalhe';
        $this->processoAp = 58;
    }
};
