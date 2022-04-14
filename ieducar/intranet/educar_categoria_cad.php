<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $id;
    public $descricao;
    public $observacoes;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11, 'educar_categoria_lst.php');

        if (is_numeric($this->id)) {
            $obj = new clsPmieducarCategoriaObra($this->id);
            $registro = $obj->detalhe();
            if ($registro) {
                //passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_categoria_obra_det.php?id={$registro['id']}" : 'educar_categoria_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' categoria', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('id', $this->id);
        $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 30, 255, true);
        $this->campoMemo('observacoes', 'Observa&ccedil;&otilde;es', $this->observacoes, 60, 5, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11, 'educar_categoria_lst.php');

        $obj = new clsPmieducarCategoriaObra(0, $this->descricao, $this->observacoes);
        $this->id = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->id = $this->id;

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11, 'educar_categoria_lst.php');

        $obj = new clsPmieducarCategoriaObra($this->id, $this->descricao, $this->observacoes);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11, 'educar_categoria_lst.php');

        $obj = new clsPmieducarCategoriaObra($this->id);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = 'N&atilde;o &eacute; poss&iacute;vel excluir esta categoria. Verifique se a mesma possui v&iacute;nculo com obras.<br>';

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url_script[] = 'go(\'educar_categoria_obra_det.php?id='. $this->id .'\')';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Categoria obras';
        $this->processoAp = 599;
    }
};
