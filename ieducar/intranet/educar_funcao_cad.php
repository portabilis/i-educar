<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $abreviatura;
    public $professor;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_funcao=$_GET['cod_funcao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(634, $this->pessoa_logada, 3, 'educar_funcao_lst.php');

        if (is_numeric($this->cod_funcao)) {
            $obj = new clsPmieducarFuncao($this->cod_funcao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(634, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }

            if ($this->professor == '0') {
                $this->professor =  'N';
            } elseif ($this->professor == '1') {
                $this->professor = 'S';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}" : 'educar_funcao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' função', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_funcao', $this->cod_funcao);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_funcao', 'Func&atilde;o', $this->nm_funcao, 30, 255, true);
        $this->campoTexto('abreviatura', 'Abreviatura', $this->abreviatura, 30, 30, true);
        $opcoes = ['' => 'Selecione',
                        'S' => 'Sim',
                        'N' => 'N&atilde;o'
                        ];

        $this->campoLista('professor', 'Professor', $opcoes, $this->professor, '', false, '', '', false, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(634, $this->pessoa_logada, 3, 'educar_funcao_lst.php');

        if ($this->professor == 'N') {
            $this->professor =  '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        $obj = new clsPmieducarFuncao(null, null, $this->pessoa_logada, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        if ($this->professor == 'N') {
            $this->professor =  '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(634, $this->pessoa_logada, 3, 'educar_funcao_lst.php');

        $obj = new clsPmieducarFuncao($this->cod_funcao, $this->pessoa_logada, null, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(634, $this->pessoa_logada, 3, 'educar_funcao_lst.php');

        $obj = new clsPmieducarFuncao($this->cod_funcao, $this->pessoa_logada, null, null, null, null, null, null, 0, $this->ref_cod_instituicao);

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores -  Funções do servidor';
        $this->processoAp = '634';
    }
};
