<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_categoria_nivel;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_categoria_nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_categoria_nivel=$_GET['cod_categoria_nivel'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, 'educar_categoria_nivel_lst.php', true);

        if (is_numeric($this->cod_categoria_nivel)) {
            $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(829, $this->pessoa_logada, 3, null, true)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_categoria_nivel_det.php?cod_categoria_nivel={$registro['cod_categoria_nivel']}" : 'educar_categoria_nivel_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' categoria/nível', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_categoria_nivel', $this->cod_categoria_nivel);

        // foreign keys

        // text
        $this->campoTexto('nm_categoria_nivel', 'Nome Categoria', $this->nm_categoria_nivel, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, 'educar_categoria_nivel_lst.php', true);

        $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, 'educar_categoria_nivel_lst.php', true);

        $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(829, $this->pessoa_logada, 3, 'educar_categoria_nivel_lst.php', true);

        $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, 0);

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Cadastro Categoria N&iacute;vel';
        $this->processoAp = '829';
    }
};
