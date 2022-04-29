<?php

return new class extends clsDetalhe {
    public $titulo;

    public $cod_formacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_servidor;
    public $nm_formacao;
    public $tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Servidor Formacao - Detalhe';

        $this->cod_formacao = $_GET['cod_formacao'];

        $tmp_obj = new clsPmieducarServidorFormacao($this->cod_formacao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_servidor_formacao_lst.php');
        }

        $obj_ref_cod_servidor = new clsPmieducarServidor(
            $registro['ref_cod_servidor'],
            null,
            null,
            null,
            null,
            null,
            1,
            $registro['ref_ref_cod_instituicao']
        );

        $det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();
        $registro['ref_cod_servidor'] = $det_ref_cod_servidor['cod_servidor'];

        if ($registro['nm_formacao']) {
            $this->addDetalhe(['Nome Formação', $registro['nm_formacao']]);
        }

        if ($registro['tipo'] == 'C') {
            $obj_curso = new clsPmieducarServidorCurso(null, $this->cod_formacao);
            $det_curso = $obj_curso->detalhe();
        } elseif ($registro['tipo'] == 'T' || $registro['tipo'] == 'O') {
            $obj_titulo = new clsPmieducarServidorTituloConcurso(null, $this->cod_formacao);
            $det_titulo = $obj_titulo->detalhe();
        }

        if ($registro['tipo']) {
            if ($registro['tipo'] == 'C') {
                $registro['tipo'] = 'Curso';
            } elseif ($registro['tipo'] == 'T') {
                $registro['tipo'] = 'Título';
            } else {
                $registro['tipo'] = 'Concurso';
            }

            $this->addDetalhe(['Tipo', $registro['tipo']]);
        }

        if ($registro['descricao']) {
            $this->addDetalhe(['Descricção', $registro['descricao']]);
        }

        if ($det_curso['data_conclusao']) {
            $this->addDetalhe(['Data de Conclusão', dataFromPgToBr($det_curso['data_conclusao'])]);
        }

        if ($det_curso['data_registro']) {
            $this->addDetalhe(['Data de Registro', dataFromPgToBr($det_curso['data_registro'])]);
        }

        if ($det_curso['diplomas_registros']) {
            $this->addDetalhe(['Diplomas e Registros', $det_curso['diplomas_registros']]);
        }

        if ($det_titulo['data_vigencia_homolog'] && $registro['tipo'] == 'Título') {
            $this->addDetalhe(['Data de Vigência', dataFromPgToBr($det_titulo['data_vigencia_homolog'])]);
        } elseif ($det_titulo['data_vigencia_homolog'] && $registro['tipo'] == 'Concurso') {
            $this->addDetalhe(['Data de Homologação', dataFromPgToBr($det_titulo['data_vigencia_homolog'])]);
        }

        if ($det_titulo['data_publicacao']) {
            $this->addDetalhe(['Data de Publicação', dataFromPgToBr($det_titulo['data_publicacao'])]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_servidor_formacao_cad.php';

            $this->url_editar = sprintf(
                'educar_servidor_formacao_cad.php?cod_formacao=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d',
                $registro['cod_formacao'],
                $registro['ref_ref_cod_instituicao'],
                $registro['ref_cod_servidor']
            );
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $registro['ref_cod_servidor'],
            $registro['ref_ref_cod_instituicao']
        );

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Servidor Formação';
        $this->processoAp = 635;
    }
};
