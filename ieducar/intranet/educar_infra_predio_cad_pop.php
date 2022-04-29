<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_infra_predio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $nm_predio;
    public $desc_predio;
    public $endereco;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_infra_predio=$_GET['cod_infra_predio'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(567, $this->pessoa_logada, 7, 'educar_infra_predio_lst.php');

        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_infra_predio', $this->cod_infra_predio);

        if ($_GET['precisa_lista']) {
            $obrigatorio = true;
            $get_escola  = true;
            include('include/pmieducar/educar_campo_lista.php');
        } else {
            $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
        }
        // text
        $this->campoTexto('nm_predio', 'Nome Prédio', $this->nm_predio, 30, 255, true);
        $this->campoMemo('desc_predio', 'Descrição Prédio', $this->desc_predio, 60, 10, false);
        $this->campoMemo('endereco', 'Endereço', $this->endereco, 60, 2, true);
    }

    public function Novo()
    {

        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_infra_predio').disabled)
                            parent.document.getElementById('ref_cod_infra_predio').options[0] = new Option('Selecione um prédio', '', false, false);
                        parent.document.getElementById('ref_cod_infra_predio').options[parent.document.getElementById('ref_cod_infra_predio').options.length] = new Option('$this->nm_predio', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_infra_predio').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_infra_predio').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

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
        if (! $_GET['precisa_lista']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-infra-predio-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'Infra Predio';
        $this->processoAp = '567';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
