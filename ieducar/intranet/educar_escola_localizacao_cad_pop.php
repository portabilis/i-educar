<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_escola_localizacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_localizacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $this->cod_escola_localizacao=$_GET['cod_escola_localizacao'];

        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_escola_localizacao', $this->cod_escola_localizacao);

        // Filtros de Foreign Keys
//      $obrigatorio = true;
//      include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoTexto('nm_localizacao', 'Localiza&ccedil;&atilde;o', $this->nm_localizacao, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $obj = new clsPmieducarEscolaLocalizacao(null, null, $this->pessoa_logada, $this->nm_localizacao, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_cod_escola_localizacao').options[parent.document.getElementById('ref_cod_escola_localizacao').options.length] = new Option('$this->nm_localizacao', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_escola_localizacao').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_escola_localizacao').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
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
        return file_get_contents(__DIR__ . '/scripts/extra/educar-escola-localizacao-cad-pop.js');
    }

    public function Formular()
    {
        $this->title = 'Escola Localização';
        $this->processoAp = '562';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
