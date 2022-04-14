<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_cliente_tipo;
    public $ref_cod_biblioteca;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $ref_cod_exemplar_tipo;
    public $dias_emprestimo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_cliente_tipo=$_GET['cod_cliente_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        if (is_numeric($this->cod_cliente_tipo)) {
            $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                $obj_det = $obj_biblioteca->detalhe();
                $this->ref_cod_instituicao = $obj_det['ref_cod_instituicao'];
                $this->ref_cod_escola = $obj_det['ref_cod_escola'];

                if ($obj_permissoes->permissao_excluir(596, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_cliente_tipo_det.php?cod_cliente_tipo={$registro['cod_cliente_tipo']}" : 'educar_cliente_tipo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_cliente_tipo', $this->cod_cliente_tipo);

        if ($this->cod_cliente_tipo) {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $biblioteca_desabilitado = true;
        }

        // foreign keys
        $get_escola     = 1;
        $escola_obrigatorio = false;
        $get_biblioteca = 1;
        $instituicao_obrigatorio = true;
        $biblioteca_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_tipo', 'Tipo Cliente', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);

        //-----------------------INICIO EXEMPLAR TIPO------------------------//

        $opcoes = [ '' => 'Selecione' ];
        $script .= "var editar_ = 0;\n";
        if ($_GET['cod_cliente_tipo']) {
            $script .= "editar_ = {$_GET['cod_cliente_tipo']};\n";
        }

        echo "<script>{$script}</script>";

        // se o caso é EDITAR
        if ($this->ref_cod_biblioteca) {
            $objTemp = new clsPmieducarExemplarTipo();
            $objTemp->setOrderby('nm_tipo ASC');
            $lista = $objTemp->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_exemplar_tipo']}"] = "{$registro['nm_tipo']}";
                }
            }
        }

        $this->campoRotulo('div_exemplares', 'Tipo Exemplar', '<div id=\'exemplares\'></div>');
        $this->acao_enviar = 'Valida();';
        //-----------------------FIM EXEMPLAR TIPO------------------------//
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $exemplar_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $exemplar_tipo;
            }
        }

        $obj = new clsPmieducarClienteTipo(null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1);
        $this->cod_cliente_tipo = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_cliente_tipo = $this->cod_cliente_tipo;

            //-----------------------CADASTRA EXEMPLAR TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $exemplar_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($cadastrou, $exemplar_tipo, $dias_emprestimo);
                    $cadastrou2  = $obj->cadastra();
                    if (!$cadastrou2) {
                        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                        return false;
                    }
                }
            }
            //-----------------------FIM CADASTRA EXEMPLAR TIPO------------------------//

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $exemplar_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $exemplar_tipo;
            }
        }

        $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            //-----------------------EDITA EXEMPLAR TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $exemplar_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($this->cod_cliente_tipo, $exemplar_tipo, $dias_emprestimo);

                    if ($obj->existe() == false) {
                        $result = $obj->cadastra();
                    } else {
                        $result = $obj->edita();
                    }

                    if (! $result) {
                        $this->mensagem = 'Aparentemente ocorreu um erro ao gravar os dias de emprestimo.<br>';

                        return false;
                    }
                }
            }

            //-----------------------FIM EDITA EXEMPLAR TIPO------------------------//

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-cliente-tipo-cad.js');
    }

    public function makeCss()
    {
        return file_get_contents(__DIR__ . '/styles/extra/educar-cliente-tipo-cad.css');
    }

    public function Formular()
    {
        $this->title = 'Tipo Cliente';
        $this->processoAp = '596';
    }
};
