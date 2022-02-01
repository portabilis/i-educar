<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_exemplar_tipo;
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

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_exemplar_tipo=$_GET['cod_exemplar_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_exemplar_tipo_lst.php');

        if (is_numeric($this->cod_exemplar_tipo)) {
            $obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($this->cod_exemplar_tipo) {
                    $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                    $det_biblioteca = $obj_biblioteca->detalhe();
                    $this->ref_cod_instituicao = $det_biblioteca['ref_cod_instituicao'];
                    $this->ref_cod_escola = $det_biblioteca['ref_cod_escola'];
                }

                if ($obj_permissoes->permissao_excluir(597, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_exemplar_tipo_det.php?cod_exemplar_tipo={$registro['cod_exemplar_tipo']}" : 'educar_exemplar_tipo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de exemplar', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_exemplar_tipo', $this->cod_exemplar_tipo);

        if ($this->cod_exemplar_tipo) {
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
        $this->campoTexto('nm_tipo', 'Tipo Exemplar', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);

        //-----------------------INICIO CLIENTE TIPO------------------------//

        $opcoes = [ '' => 'Selecione' ];
        $todos_tipos_clientes .= "var editar_ = 0;\n";
        if ($_GET['cod_exemplar_tipo']) {
            $todos_tipos_clientes .= "editar_ = {$_GET['cod_exemplar_tipo']};\n";
        }

        echo "<script>{$todos_tipos_clientes}{$script}</script>";

        // se o caso é EDITAR
        if ($this->ref_cod_biblioteca) {
            $objTemp = new clsPmieducarClienteTipo();
            $objTemp->setOrderby('nm_tipo ASC');
            $lista = $objTemp->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_cliente_tipo']}"] = "{$registro['nm_tipo']}";
                }
            }
        }

        $this->campoRotulo('div_clientes', 'Tipo Cliente', '<div id=\'clientes\'></div>');
        $this->acao_enviar = 'Valida();';
        //-----------------------FIM CLIENTE TIPO------------------------
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_exemplar_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $cliente_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $cliente_tipo;
            }
        }

        $obj = new clsPmieducarExemplarTipo(null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1);
        $this->cod_exemplar_tipo = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_exemplar_tipo = $this->cod_exemplar_tipo;

            //-----------------------CADASTRA CLIENTE TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $cliente_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($cliente_tipo, $cadastrou, $dias_emprestimo);
                    $cadastrou2  = $obj->cadastra();
                    if (!$cadastrou2) {
                        $this->mensagem = 'Cadastro não realizado.<br>';

                        return false;
                    }
                }
            }
            //-----------------------FIM CADASTRA CLIENTE TIPO------------------------//

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_exemplar_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_exemplar_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $cliente_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $cliente_tipo;
            }
        }

        $obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            //-----------------------EDITA CLIENTE TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $cliente_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($cliente_tipo, $this->cod_exemplar_tipo, $dias_emprestimo);
                    $editou2  = $obj->edita();
                    if (!$editou2) {
                        $this->mensagem = 'Edição não realizada.<br>';

                        return false;
                    }
                }
            }
            //-----------------------FIM EDITA CLIENTE TIPO------------------------//

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_exemplar_tipo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(597, $this->pessoa_logada, 11, 'educar_exemplar_tipo_lst.php');

        $obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_exemplar_tipo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-exemplar-tipo-cad.js');
    }

    public function makeCss()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-exemplar-tipo-cad.css');
    }

    public function Formular()
    {
        $this->title = 'Tipo Exemplar';
        $this->processoAp = '597';
    }
};
