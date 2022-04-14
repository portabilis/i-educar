<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_falta_aluno;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $disc_ref_ref_cod_serie;
    public $disc_ref_ref_cod_escola;
    public $disc_ref_ref_cod_disciplina;
    public $disc_ref_ref_cod_turma;
    public $ref_ref_cod_turma;
    public $ref_ref_cod_matricula;
    public $data_falta;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_falta_aluno=$_GET['cod_falta_aluno'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(0, $this->pessoa_logada, 0, 'educar_falta_aluno_lst.php');

        if (is_numeric($this->cod_falta_aluno)) {
            $obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(0, $this->pessoa_logada, 0)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_falta_aluno_det.php?cod_falta_aluno={$registro['cod_falta_aluno']}" : 'educar_falta_aluno_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_falta_aluno', $this->cod_falta_aluno);

        // foreign keys
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarMatriculaTurma();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_matricula']}"] = "{$registro['data_cadastro']}";
            }
        }

        $this->campoLista('ref_ref_cod_matricula', 'Matricula', $opcoes, $this->ref_ref_cod_matricula);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarMatriculaTurma();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_matricula']}"] = "{$registro['data_cadastro']}";
            }
        }

        $this->campoLista('ref_ref_cod_turma', 'Turma', $opcoes, $this->ref_ref_cod_turma);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTurmaDisciplina();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
            }
        }

        $this->campoLista('disc_ref_ref_cod_turma', 'Disc Cod Turma', $opcoes, $this->disc_ref_ref_cod_turma);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTurmaDisciplina();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
            }
        }

        $this->campoLista('disc_ref_ref_cod_disciplina', 'Disc Cod Disciplina', $opcoes, $this->disc_ref_ref_cod_disciplina);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTurmaDisciplina();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
            }
        }

        $this->campoLista('disc_ref_ref_cod_escola', 'Disc Cod Escola', $opcoes, $this->disc_ref_ref_cod_escola);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTurmaDisciplina();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['ref_cod_turma']}"] = "{$registro['']}";
            }
        }

        $this->campoLista('disc_ref_ref_cod_serie', 'Disc Cod Serie', $opcoes, $this->disc_ref_ref_cod_serie);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(0, $this->pessoa_logada, 0, 'educar_falta_aluno_lst.php');

        $obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_falta_aluno_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(0, $this->pessoa_logada, 0, 'educar_falta_aluno_lst.php');

        $obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_falta_aluno_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(0, $this->pessoa_logada, 0, 'educar_falta_aluno_lst.php');

        $obj = new clsPmieducarFaltaAluno($this->cod_falta_aluno, $this->pessoa_logada, $this->pessoa_logada, $this->disc_ref_ref_cod_serie, $this->disc_ref_ref_cod_escola, $this->disc_ref_ref_cod_disciplina, $this->disc_ref_ref_cod_turma, $this->ref_ref_cod_turma, $this->ref_ref_cod_matricula, $this->data_falta, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_falta_aluno_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Falta Aluno';
        $this->processoAp = '0';
    }
};
