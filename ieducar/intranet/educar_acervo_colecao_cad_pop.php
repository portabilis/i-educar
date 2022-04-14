<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acervo_colecao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_colecao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11, 'educar_acervo_colecao_lst.php');

        //$this->url_cancelar = "";
        //$this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    public function Gerar()
    {
        echo '<script>window.onload=function(){parent.EscondeDiv(\'LoadImprimir\')}</script>';
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        $this->campoTexto('nm_colecao', 'Cole&ccedil;&atilde;o', $this->nm_colecao, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11, 'educar_acervo_colecao_lst.php');

        $obj = new clsPmieducarAcervoColecao($this->cod_acervo_colecao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_colecao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $this->cod_acervo_colecao = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo_colecao = $this->cod_acervo_colecao;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            echo "<script>
                    parent.document.getElementById('colecao').value = '$cadastrou';
                    parent.document.getElementById('ref_cod_acervo_colecao').disabled = false;
                    parent.document.getElementById('tipoacao').value = '';
                    parent.document.getElementById('formcadastro').submit();
                 </script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-acervo-autor-cad-pop.js');
    }

    public function Formular()
    {
        $this->title = 'Coleção';
        $this->processoAp = '593';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
