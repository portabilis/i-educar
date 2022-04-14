<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $ref_cod_matricula;
    public $ref_cod_tipo_ocorrencia_disciplinar;
    public $sequencial;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Matricula Ocorrencia Disciplinar - Detalhe';

        $this->sequencial=$_GET['sequencial'];
        $this->ref_cod_matricula=$_GET['ref_cod_matricula'];
        $this->ref_cod_tipo_ocorrencia_disciplinar=$_GET['ref_cod_tipo_ocorrencia_disciplinar'];

        $tmp_obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial, null, null, null, null, null, 1);
        $registro = $tmp_obj->detalhe();
        if (! $registro) {
            $this->simpleRedirect("educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}");
        }

        $obj_ref_cod_matricula = new clsPmieducarMatricula($registro['ref_cod_matricula']);
        $det_ref_cod_matricula = $obj_ref_cod_matricula->detalhe();
        //$registro["ref_cod_matricula"] = $det_ref_cod_matricula["ref_cod_matricula"];

        $obj_serie = new clsPmieducarSerie($det_ref_cod_matricula['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

        $obj_ref_cod_tipo_ocorrencia_disciplinar = new clsPmieducarTipoOcorrenciaDisciplinar($registro['ref_cod_tipo_ocorrencia_disciplinar']);
        $det_ref_cod_tipo_ocorrencia_disciplinar = $obj_ref_cod_tipo_ocorrencia_disciplinar->detalhe();
        $registro['nm_tipo'] = $det_ref_cod_tipo_ocorrencia_disciplinar['nm_tipo'];

        $obj_mat_turma = new clsPmieducarMatriculaTurma();

        $det_mat_turma = $obj_mat_turma->lista($registro['ref_cod_matricula'], null, null, null, null, null, null, null, 1);

        if ($det_mat_turma) {
            $det_mat_turma = array_shift($det_mat_turma);
        }

        $obj_ref_cod_tipo_ocorrencia_disciplinar = new clsPmieducarTipoOcorrenciaDisciplinar($registro['ref_cod_tipo_ocorrencia_disciplinar']);
        $det_ref_cod_tipo_ocorrencia_disciplinar = $obj_ref_cod_tipo_ocorrencia_disciplinar->detalhe();
        $registro['nm_tipo'] = $det_ref_cod_tipo_ocorrencia_disciplinar['nm_tipo'];

        if ($registro['ref_cod_matricula']) {
            $this->addDetalhe([ 'Matr&iacute;cula', "{$registro['ref_cod_matricula']}"]);
        }

        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'], null, null, null, null, null, null, null, null, null, 1));

        $this->addDetalhe(['Nome do Aluno',$det_aluno['nome_aluno']]);

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe([ 'S&eacute;rie', "{$registro['ref_ref_cod_serie']}"]);
        }

        if ($det_mat_turma['det_turma']) {
            $this->addDetalhe([ 'Turma', "{$det_mat_turma['det_turma']}"]);
        }

        if ($registro['sequencial']) {
            $this->addDetalhe([ 'N&uacute;mero da Ocorr&ecirc;ncia', "{$registro['sequencial']}"]);
        }
        if ($registro['data_cadastro']) {
            if ($hora = dataFromPgToBr("{$registro['data_cadastro']}", 'H:i')) {
                $this->addDetalhe([ 'Hora Ocorr&ecirc;ncia', $hora ]);
            }
            $this->addDetalhe([ 'Data Ocorr&ecirc;ncia', dataFromPgToBr("{$registro['data_cadastro']}", 'd/m/Y') ]);
        }
        if ($registro['ref_cod_tipo_ocorrencia_disciplinar']) {
            $this->addDetalhe([ 'Tipo Ocorr&ecirc;ncia', "{$registro['nm_tipo']}"]);
        }
        if ($registro['observacao']) {
            $this->addDetalhe([ 'Observa&ccedil;&atilde;o', nl2br("{$registro['observacao']}")]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->url_novo = "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}";
            $this->url_editar = "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}";
        }

        $this->url_cancelar = "educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['ref_cod_matricula']}";
        $this->largura = '100%';

        $this->breadcrumb('Ocorrências disciplinares da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ocorr&ecirc;ncia Disciplinar';
        $this->processoAp = '578';
    }
};
